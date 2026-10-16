import assert from "node:assert/strict";
import test from "node:test";
import { resolveNotice, withNotice } from "./notices";
import { likePattern, toInt } from "./search";

test("LIKE wildcards in a search are matched literally", () => {
  assert.equal(likePattern("100%"), "%100\\%%");
  assert.equal(likePattern("a_b"), "%a\\_b%");
  assert.equal(likePattern("c:\\x"), "%c:\\\\x%");
  assert.equal(likePattern("plain"), "%plain%");
});

test("query-string numbers fall back instead of throwing", () => {
  assert.equal(toInt("7"), 7);
  assert.equal(toInt("7abc"), 7);
  assert.equal(toInt("abc"), 0);
  assert.equal(toInt(undefined, 1), 1);
  assert.equal(toInt(["3", "4"]), 3);
});

test("banners come only from known codes", () => {
  assert.equal(resolveNotice("book_added")?.type, "success");
  assert.equal(resolveNotice("<script>alert(1)</script>"), null);
  assert.equal(resolveNotice("constructor"), null);
  assert.equal(resolveNotice(undefined), null);
  assert.equal(withNotice("/admin/books", "book_added"), "/admin/books?notice=book_added");
  assert.equal(withNotice("/catalog?x=1", "book_not_found"), "/catalog?x=1&notice=book_not_found");
});
