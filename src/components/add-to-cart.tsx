"use client";

import { useState, useTransition } from "react";
import { Icon } from "@/components/icon";
import { toast } from "@/lib/toast";
import { addToCartAction } from "@/server/cart-actions";

/** Round overlay button on a catalog card. */
export function QuickAddButton({ bookId, title }: { bookId: number; title: string }) {
  const [pending, start] = useTransition();
  return (
    <button
      type="button"
      className="user-overlay-btn add-to-cart"
      title="Add to Cart"
      aria-label={`Add ${title} to cart`}
      disabled={pending}
      onClick={() =>
        start(async () => {
          const result = await addToCartAction(bookId, 1);
          toast(result.message, result.ok ? "success" : "error");
        })
      }
    >
      <Icon name="cart-plus-linear" width={20} />
    </button>
  );
}

/** Quantity picker and the Add to Cart button on the book page. */
export function AddToCartPanel({ bookId, stock }: { bookId: number; stock: number }) {
  const [quantity, setQuantity] = useState(1);
  const [pending, start] = useTransition();
  const max = Math.min(stock, 20);

  return (
    <>
      <div className="user-quantity-selector">
        <div className="user-quantity-controls">
          <button type="button" className="user-quantity-btn" aria-label="Decrease quantity" onClick={() => setQuantity((q) => Math.max(1, q - 1))} disabled={quantity <= 1}>
            <Icon name="minus-linear" width={18} />
          </button>
          <span className="user-quantity-value" id="quantity" aria-live="polite">
            {quantity}
          </span>
          <button type="button" className="user-quantity-btn" aria-label="Increase quantity" onClick={() => setQuantity((q) => Math.min(max, q + 1))} disabled={quantity >= max}>
            <Icon name="add-linear" width={18} />
          </button>
        </div>
      </div>

      <div className="user-detail-actions">
        <button
          type="button"
          className="user-btn user-btn-primary add-to-cart"
          disabled={pending}
          onClick={() =>
            start(async () => {
              const result = await addToCartAction(bookId, quantity);
              toast(result.message, result.ok ? "success" : "error");
            })
          }
        >
          <Icon name="cart-plus-linear" width={20} />
          {pending ? "Adding..." : "Add to Cart"}
        </button>
      </div>
    </>
  );
}
