import type { Metadata } from "next";
import Link from "next/link";
import { Suspense } from "react";
import { BookCard } from "@/components/book-card";
import { Icon } from "@/components/icon";
import { SortSelect } from "@/components/sort-select";
import { requireCustomer } from "@/lib/auth";
import { listCategories, parseSort, searchBooks, SORT_OPTIONS, toInt } from "@/server/queries";

export const metadata: Metadata = { title: "Book Catalog" };

type Params = { category?: string; search?: string; sort?: string; page?: string };

function href(params: { category?: number; search?: string; sort?: string; page?: number }) {
  const query = new URLSearchParams();
  if (params.category) query.set("category", String(params.category));
  if (params.search) query.set("search", params.search);
  if (params.sort && params.sort !== "title_asc") query.set("sort", params.sort);
  if (params.page && params.page > 1) query.set("page", String(params.page));
  const text = query.toString();
  return text ? `/catalog?${text}` : "/catalog";
}

export default async function CatalogPage({ searchParams }: { searchParams: Promise<Params> }) {
  await requireCustomer("/catalog");
  const params = await searchParams;
  const categoryId = toInt(params.category) || undefined;
  const search = (params.search ?? "").trim().slice(0, 100);
  const sort = parseSort(params.sort);

  const [categories, result] = await Promise.all([
    listCategories(),
    searchBooks({ categoryId, search, sort, page: toInt(params.page, 1) }),
  ]);
  const current = { category: categoryId, search, sort };

  return (
    <>
      <h1 className="user-page-title">Book Catalog</h1>

      <div className="user-catalog-layout">
        <aside className="user-sidebar" aria-label="Categories">
          <div className="user-sidebar-section">
            <h2 className="user-sidebar-title">Categories</h2>
            <ul className="user-category-list">
              <li>
                <Link href={href({ search, sort })} className={!categoryId ? "active" : ""}>
                  All Books
                </Link>
              </li>
              {categories.map((category) => (
                <li key={category.id}>
                  <Link href={href({ category: category.id, search, sort })} className={categoryId === category.id ? "active" : ""}>
                    {category.name}
                  </Link>
                </li>
              ))}
            </ul>
          </div>
        </aside>

        <div className="user-catalog-main">
          <form method="GET" action="/catalog" className="user-search-form" role="search">
            {categoryId ? <input type="hidden" name="category" value={categoryId} /> : null}
            {sort !== "title_asc" ? <input type="hidden" name="sort" value={sort} /> : null}
            <div className="user-search-input-wrapper">
              <Icon name="magnifer-linear" width={20} />
              <input type="search" name="search" placeholder="Search books by title, author, or ISBN..." defaultValue={search} className="user-search-input" aria-label="Search books" maxLength={100} />
            </div>
            <button type="submit" className="user-btn user-btn-primary">
              Search
            </button>
          </form>

          <div className="user-catalog-controls">
            <Suspense>
              <SortSelect value={sort} options={SORT_OPTIONS} />
            </Suspense>
            <div className="user-results-count">
              Showing {result.rows.length} of {result.total} books
            </div>
          </div>

          {search ? (
            <p style={{ fontSize: "0.875rem", color: "var(--user-zinc-500)", marginBottom: "1.5rem" }}>
              Found {result.total} result(s) for &quot;{search}&quot;
              <Link href={href({ category: categoryId, sort })} style={{ color: "var(--user-zinc-900)", textDecoration: "underline", marginLeft: "0.5rem" }}>
                Clear
              </Link>
            </p>
          ) : null}

          {result.rows.length === 0 ? (
            <div className="user-cart-empty">
              <p>No books found.</p>
              <Link href="/catalog" className="user-btn user-btn-primary">
                View All Books
              </Link>
            </div>
          ) : (
            <>
              <div className="user-books-grid user-fade-in">
                {result.rows.map((book) => (
                  <BookCard key={book.id} book={book} />
                ))}
              </div>

              {result.pages > 1 ? (
                <nav className="user-pagination" aria-label="Pagination">
                  {result.page > 1 ? (
                    <Link href={href({ ...current, page: result.page - 1 })} className="user-pagination-btn" aria-label="Previous page">
                      <Icon name="alt-arrow-left-linear" width={16} />
                    </Link>
                  ) : null}
                  <span className="user-pagination-info">
                    Page {result.page} of {result.pages}
                  </span>
                  {result.page < result.pages ? (
                    <Link href={href({ ...current, page: result.page + 1 })} className="user-pagination-btn" aria-label="Next page">
                      <Icon name="alt-arrow-right-linear" width={16} />
                    </Link>
                  ) : null}
                </nav>
              ) : null}
            </>
          )}
        </div>
      </div>
    </>
  );
}
