import Link from "next/link";
import { Icon } from "@/components/icon";
import { QuickAddButton } from "@/components/add-to-cart";
import { BookCover } from "@/components/book-cover";
import { formatCents } from "@/lib/money";

export function StockBadge({ stock, detail = false }: { stock: number; detail?: boolean }) {
  if (stock > 10) return <span className="user-stock-badge in-stock">{detail ? `In Stock (${stock} available)` : "In Stock"}</span>;
  if (stock > 0) return <span className="user-stock-badge low-stock">{detail ? `Low Stock (${stock} available)` : "Low Stock"}</span>;
  return <span className="user-stock-badge out-of-stock">Out of Stock</span>;
}

type CardBook = { id: number; title: string; author: string; priceCents: number; coverImage: string | null; stock?: number };

/** `showActions` adds the hover overlay with View and Quick Add. Related-book cards leave it off. */
export function BookCard({ book, showActions = true }: { book: CardBook; showActions?: boolean }) {
  return (
    <div className="user-book-card">
      <div className="user-book-image-wrapper">
        <BookCover src={book.coverImage} title={book.title} />
        {showActions ? (
          <div className="user-book-overlay">
            <Link href={`/books/${book.id}`} className="user-overlay-btn" title="View Details" aria-label={`View ${book.title}`}>
              <Icon name="eye-linear" width={20} />
            </Link>
            {(book.stock ?? 0) > 0 ? <QuickAddButton bookId={book.id} title={book.title} /> : null}
          </div>
        ) : null}
      </div>
      <h3 className="user-book-title">
        <Link href={`/books/${book.id}`}>{book.title}</Link>
      </h3>
      <p className="user-book-author">{book.author}</p>
      <div className="user-book-footer">
        <span className="user-book-price">{formatCents(book.priceCents)}</span>
        {book.stock !== undefined ? <StockBadge stock={book.stock} /> : null}
      </div>
    </div>
  );
}
