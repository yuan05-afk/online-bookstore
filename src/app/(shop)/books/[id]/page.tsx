import type { Metadata } from "next";
import Link from "next/link";
import { redirect } from "next/navigation";
import { AddToCartPanel } from "@/components/add-to-cart";
import { BookCard, StockBadge } from "@/components/book-card";
import { BookCover } from "@/components/book-cover";
import { Icon } from "@/components/icon";
import { requireCustomer } from "@/lib/auth";
import { formatCents } from "@/lib/money";
import { withNotice } from "@/lib/notices";
import { getBook, relatedBooks } from "@/server/queries";

type Props = { params: Promise<{ id: string }> };

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const book = await getBook(Number((await params).id));
  return { title: book?.title ?? "Book" };
}

export default async function BookPage({ params }: Props) {
  const { id } = await params;
  await requireCustomer(`/books/${id}`);
  const book = await getBook(Number(id));
  if (!book) redirect(withNotice("/catalog", "book_not_found"));
  const related = await relatedBooks(book);

  return (
    <>
      <Link href="/catalog" className="user-back-link">
        <Icon name="alt-arrow-left-linear" width={16} />
        Back to Catalog
      </Link>

      <div className="user-detail-grid">
        <div className="user-detail-image">
          <BookCover src={book.coverImage} title={book.title} placeholderText="No Image Available" />
        </div>

        <div className="user-detail-info">
          <div>
            <h1 className="user-detail-title">{book.title}</h1>
            <p className="user-detail-author">by {book.author}</p>
          </div>

          <div className="user-detail-price">{formatCents(book.priceCents)}</div>

          <div className="user-detail-meta">
            <p>
              <strong>ISBN:</strong> {book.isbn}
            </p>
            {book.categoryName ? (
              <p>
                <strong>Category:</strong>{" "}
                <Link href={`/catalog?category=${book.categoryId}`} style={{ color: "var(--user-zinc-900)", textDecoration: "underline" }}>
                  {book.categoryName}
                </Link>
              </p>
            ) : null}
            <p>
              <strong>Availability:</strong> <StockBadge stock={book.stock} detail />
            </p>
          </div>

          {book.description ? (
            <div>
              <h2 style={{ fontSize: "0.875rem", fontWeight: 600, color: "var(--user-zinc-900)", marginBottom: "0.75rem" }}>Description</h2>
              <p className="user-detail-description" style={{ whiteSpace: "pre-line" }}>
                {book.description}
              </p>
            </div>
          ) : null}

          {book.stock > 0 ? <AddToCartPanel bookId={book.id} stock={book.stock} /> : null}
        </div>
      </div>

      {related.length > 0 ? (
        <div className="user-related-section">
          <h2 className="user-related-title">Related Books</h2>
          <div className="user-books-grid">
            {related.map((item) => (
              <BookCard key={item.id} book={item} showActions={false} />
            ))}
          </div>
        </div>
      ) : null}
    </>
  );
}
