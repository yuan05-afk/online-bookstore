import type { Metadata } from "next";
import Link from "next/link";
import { BookCover } from "@/components/book-cover";
import { QuantityStepper, RemoveButton } from "@/components/cart-controls";
import { Icon } from "@/components/icon";
import { requireCustomer } from "@/lib/auth";
import { formatCents, TAX_RATE, totals } from "@/lib/money";
import { cartLines } from "@/server/queries";

export const metadata: Metadata = { title: "Shopping Cart" };

export default async function CartPage() {
  const user = await requireCustomer("/cart");
  const lines = await cartLines(user.id);
  const sums = totals(lines);

  return (
    <>
      <h1 className="user-page-title">Shopping Cart</h1>

      {lines.length === 0 ? (
        <div className="user-cart-empty">
          <Icon name="cart-large-linear" width={64} style={{ color: "var(--user-zinc-300)" }} />
          <p>Your cart is empty</p>
          <Link href="/catalog" className="user-btn user-btn-primary">
            Continue Shopping
          </Link>
        </div>
      ) : (
        <div className="user-cart-grid">
          <div className="user-cart-items">
            {lines.map((line) => (
              <div key={line.id} className="user-cart-item">
                <div className="user-cart-thumbnail">
                  <BookCover src={line.coverImage} title={line.title} />
                </div>
                <div className="user-cart-details">
                  <div className="user-cart-item-header">
                    <div>
                      <h3 className="user-cart-item-title">
                        <Link href={`/books/${line.bookId}`}>{line.title}</Link>
                      </h3>
                      <p className="user-cart-item-author">{line.author}</p>
                      {line.quantity > line.stock ? (
                        <p style={{ color: "var(--user-red-600)", fontSize: "0.8125rem" }}>Only {line.stock} in stock. Lower the quantity to check out.</p>
                      ) : null}
                    </div>
                    <RemoveButton itemId={line.id} title={line.title} />
                  </div>
                  <div className="user-cart-item-footer">
                    <QuantityStepper itemId={line.id} quantity={line.quantity} max={line.stock} />
                    <span className="user-cart-item-price">{formatCents(line.priceCents * line.quantity)}</span>
                  </div>
                </div>
              </div>
            ))}
          </div>

          <div className="user-cart-summary">
            <h2 className="user-cart-summary-title">Order Summary</h2>
            <div className="user-summary-rows">
              <div className="user-summary-row">
                <span>Subtotal:</span>
                <span>{formatCents(sums.subtotalCents)}</span>
              </div>
              <div className="user-summary-row">
                <span>Tax ({(TAX_RATE * 100).toFixed(1).replace(/\.0$/, "")}%):</span>
                <span>{formatCents(sums.taxCents)}</span>
              </div>
              <div className="user-summary-row user-summary-total">
                <span>Total:</span>
                <span>{formatCents(sums.totalCents)}</span>
              </div>
            </div>
            <Link href="/checkout" className="user-btn user-btn-primary user-btn-block">
              <Icon name="card-linear" width={20} />
              Proceed to Checkout
            </Link>
            <Link href="/catalog" className="user-btn user-btn-secondary user-btn-block">
              Continue Shopping
            </Link>
          </div>
        </div>
      )}
    </>
  );
}
