"use client";

import { useTransition } from "react";
import { useRouter } from "next/navigation";
import { Icon } from "@/components/icon";
import { toast } from "@/lib/toast";
import { removeCartItemAction, setCartQuantityAction } from "@/server/cart-actions";

export function QuantityStepper({ itemId, quantity, max }: { itemId: number; quantity: number; max: number }) {
  const router = useRouter();
  const [pending, start] = useTransition();

  function change(next: number) {
    start(async () => {
      const result = await setCartQuantityAction(itemId, next);
      if (!result.ok) toast(result.message, "error");
      router.refresh();
    });
  }

  return (
    <div className="user-cart-quantity" aria-busy={pending}>
      <button type="button" aria-label="Decrease quantity" onClick={() => change(quantity - 1)} disabled={pending || quantity <= 1}>
        <Icon name="minus-circle-linear" width={18} />
      </button>
      <span aria-live="polite">{quantity}</span>
      <button type="button" aria-label="Increase quantity" onClick={() => change(quantity + 1)} disabled={pending || quantity >= Math.min(max, 20)}>
        <Icon name="add-circle-linear" width={18} />
      </button>
    </div>
  );
}

export function RemoveButton({ itemId, title }: { itemId: number; title: string }) {
  const router = useRouter();
  const [pending, start] = useTransition();
  return (
    <button
      type="button"
      className="user-cart-remove-btn"
      title="Remove"
      aria-label={`Remove ${title} from cart`}
      disabled={pending}
      onClick={() => {
        if (!window.confirm(`Remove "${title}" from your cart?`)) return;
        start(async () => {
          const result = await removeCartItemAction(itemId);
          if (!result.ok) toast(result.message, "error");
          router.refresh();
        });
      }}
    >
      <Icon name="trash-bin-trash-linear" width={20} />
    </button>
  );
}
