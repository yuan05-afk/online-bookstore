import type { Metadata } from "next";
import Link from "next/link";
import { AdminCover } from "@/components/admin-ui";
import { DeleteBookButton } from "@/components/delete-book-button";
import { Icon } from "@/components/icon";
import { formatCents } from "@/lib/money";
import { adminBooks, listCategories, toInt } from "@/server/queries";

export const metadata: Metadata = { title: "Manage Books" };

export default async function AdminBooksPage({ searchParams }: { searchParams: Promise<{ search?: string; category?: string }> }) {
  const params = await searchParams;
  const search = (params.search ?? "").trim().slice(0, 100);
  const categoryId = toInt(params.category) || undefined;
  const [rows, categories] = await Promise.all([adminBooks({ search, categoryId }), listCategories()]);

  return (
    <>
      <div className="admin-flex-between admin-mb-6">
        <h1 className="admin-page-title" style={{ margin: 0 }}>
          Book Management
        </h1>
        <Link href="/admin/books/new" className="admin-btn admin-btn-primary">
          <Icon name="add-circle-linear" width={18} />
          Add New Book
        </Link>
      </div>

      <form method="GET" action="/admin/books" className="admin-filter-bar" role="search">
        <div className="admin-input-icon">
          <Icon name="magnifer-linear" width={18} />
          <input type="search" name="search" className="admin-input" placeholder="Search books..." defaultValue={search} aria-label="Search books" />
        </div>
        <select name="category" className="admin-select" defaultValue={categoryId ? String(categoryId) : ""} aria-label="Category">
          <option value="">All Categories</option>
          {categories.map((category) => (
            <option key={category.id} value={category.id}>
              {category.name}
            </option>
          ))}
        </select>
        <button type="submit" className="admin-btn admin-btn-primary">
          Filter
        </button>
        {search || categoryId ? (
          <Link href="/admin/books" className="admin-btn admin-btn-secondary">
            Clear
          </Link>
        ) : null}
      </form>

      <div className="admin-card">
        <div className="admin-table-wrapper">
          <table className="admin-table">
            <thead>
              <tr>
                <th style={{ width: 60 }}>Cover</th>
                <th>Book Info</th>
                <th>Category</th>
                <th>Price</th>
                <th>Stock</th>
                <th className="text-right">Actions</th>
              </tr>
            </thead>
            <tbody>
              {rows.length === 0 ? (
                <tr>
                  <td colSpan={6} className="admin-text-center admin-text-muted" style={{ padding: "3rem" }}>
                    No books found
                  </td>
                </tr>
              ) : (
                rows.map((book) => (
                  <tr key={book.id}>
                    <td>
                      <AdminCover src={book.coverImage} title={book.title} />
                    </td>
                    <td>
                      <div style={{ fontWeight: "var(--admin-font-weight-medium)", color: "var(--admin-text-primary)", marginBottom: "0.25rem" }}>{book.title}</div>
                      <div style={{ fontSize: "var(--admin-font-size-xs)", color: "var(--admin-text-secondary)" }}>{book.author}</div>
                    </td>
                    <td className="admin-text-secondary">{book.categoryName ?? "Uncategorized"}</td>
                    <td>
                      <strong>{formatCents(book.priceCents)}</strong>
                    </td>
                    <td>
                      <span className={`admin-badge admin-badge-${book.stock === 0 ? "danger" : book.stock < 10 ? "warning" : "success"}`}>
                        {book.stock === 0 ? "Out of stock" : `${book.stock} in stock`}
                      </span>
                    </td>
                    <td>
                      <div className="admin-table-actions">
                        <Link href={`/admin/books/${book.id}/edit`} className="admin-btn-icon" title="Edit" aria-label={`Edit ${book.title}`}>
                          <Icon name="pen-linear" width={18} />
                        </Link>
                        <DeleteBookButton id={book.id} title={book.title} />
                      </div>
                    </td>
                  </tr>
                ))
              )}
            </tbody>
          </table>
        </div>
      </div>
    </>
  );
}
