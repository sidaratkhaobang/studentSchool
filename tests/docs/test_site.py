"""Build and verify the same static documentation artifact deployed by CI."""

import json
from html.parser import HTMLParser
from pathlib import Path
import re
import subprocess
import sys
import tempfile
import unittest
from urllib.parse import unquote, urljoin, urlsplit


ROOT = Path(__file__).resolve().parents[2]
SITE_URL = "https://sidaratkhaobang.github.io/studentSchool/"
PAGES = (
    "index.html",
    "ProjectStructure.html",
    "Modules.html",
    "BusinessRules.html",
    "SRS.html",
    "ER_Diagram.html",
    "Data_Dictionary.html",
    "TestCases.html",
    "ProjectScope.html",
)


class PageParser(HTMLParser):
    def __init__(self, source):
        super().__init__()
        self.ids = set()
        self.links = []
        self.feed(source)

    def handle_starttag(self, tag, attributes):
        attributes = dict(attributes)
        if "id" in attributes:
            self.ids.add(attributes["id"])
        if tag in ("a", "link") and attributes.get("href"):
            self.links.append(attributes["href"])
        if tag in ("script", "img") and attributes.get("src"):
            self.links.append(attributes["src"])


class DocumentationSiteTest(unittest.TestCase):
    @classmethod
    def setUpClass(cls):
        cls.temporary = tempfile.TemporaryDirectory()
        cls.addClassCleanup(cls.temporary.cleanup)
        cls.site = Path(cls.temporary.name) / "site"
        result = subprocess.run(
            [sys.executable, "-m", "mkdocs", "build", "--strict", "--site-dir", str(cls.site)],
            cwd=ROOT,
            capture_output=True,
            text=True,
        )
        if result.returncode:
            raise AssertionError(result.stdout + result.stderr)

    def test_required_pages_are_built_with_navigation(self):
        for page in PAGES:
            with self.subTest(page=page):
                html = (self.site / page).read_text(encoding="utf-8")
                self.assertIn('<html lang="th"', html)
                self.assertIn("<h1", html)
                parser = PageParser(html)
                for destination in PAGES:
                    self.assertIn(destination, parser.links)

    def test_internal_links_anchors_and_assets_resolve_under_project_path(self):
        for page in self.site.rglob("*.html"):
            origin = SITE_URL + page.relative_to(self.site).as_posix()
            for link in PageParser(page.read_text(encoding="utf-8")).links:
                target = urlsplit(urljoin(origin, link))
                if target.netloc != urlsplit(SITE_URL).netloc:
                    continue
                with self.subTest(page=page.name, link=link):
                    self.assertTrue(target.path.startswith("/studentSchool/"))
                    path = self.site / unquote(target.path.removeprefix("/studentSchool/"))
                    if path.is_dir():
                        path /= "index.html"
                    self.assertTrue(path.is_file(), str(path))
                    if target.fragment and path.suffix == ".html":
                        self.assertIn(
                            unquote(target.fragment),
                            PageParser(path.read_text(encoding="utf-8")).ids,
                        )

    def test_readme_exposes_every_html_document(self):
        readme = (ROOT / "README.md").read_text(encoding="utf-8")
        links = re.findall(r"\]\((https://sidaratkhaobang\.github\.io/studentSchool/[^)]*)\)", readme)
        paths = {urlsplit(link).path.removeprefix("/studentSchool/") or "index.html" for link in links}
        self.assertTrue(set(PAGES).issubset(paths), set(PAGES) - paths)

    def test_er_diagram_is_mermaid_with_current_teacher_fields(self):
        html = (self.site / "ER_Diagram.html").read_text(encoding="utf-8")
        self.assertIn('class="mermaid"', html)
        for field in ("erDiagram", "user_id", "learning_content", "material_path", "approved_by_teacher_id", "rejection_reason"):
            self.assertIn(field, html)

    def test_security_examples_render_as_text_not_executable_scripts(self):
        html = (self.site / "TestCases.html").read_text(encoding="utf-8")
        self.assertNotIn("<script>alert(", html)
        self.assertIn("&lt;script&gt;", html)

    def test_search_indexes_all_document_pages(self):
        data = json.loads((self.site / "search/search_index.json").read_text(encoding="utf-8"))
        indexed = {entry["location"].split("#")[0] for entry in data["docs"]}
        self.assertTrue(set(PAGES).issubset(indexed), set(PAGES) - indexed)

    def test_artifact_contains_no_application_or_runtime_files(self):
        for path in self.site.rglob("*"):
            with self.subTest(path=path):
                self.assertNotIn(path.suffix, (".php", ".sql", ".sqlite", ".log"))
                self.assertFalse(path.name.startswith(".env"))
                self.assertFalse(path.is_symlink())


if __name__ == "__main__":
    unittest.main()
