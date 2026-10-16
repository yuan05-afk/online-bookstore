import { formatCents } from "@/lib/money";
import { SITE_NAME } from "@/lib/site";

/**
 * Order confirmation email through Resend's HTTP API. The old store used SendGrid and PHPMailer.
 * Without RESEND_API_KEY nothing is sent and the order is unaffected; the confirmation page
 * and "My Orders" always show the order.
 */

type EmailLine = { title: string; quantity: number; priceCents: number };

export type OrderEmail = {
  to: string;
  customerName: string;
  orderNumber: string;
  lines: EmailLine[];
  subtotalCents: number;
  taxCents: number;
  totalCents: number;
  shippingAddress: string;
};

function escapeHtml(value: string): string {
  return value.replace(/[&<>"']/g, (char) => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" })[char] as string);
}

export function orderEmailText(order: OrderEmail): string {
  const lines = order.lines.map((line) => `  ${line.quantity} x ${line.title}  ${formatCents(line.priceCents * line.quantity)}`).join("\n");
  return [
    `Hi ${order.customerName},`,
    "",
    `Thanks for your order with ${SITE_NAME}. Your order number is ${order.orderNumber}.`,
    "",
    lines,
    "",
    `Subtotal: ${formatCents(order.subtotalCents)}`,
    `Tax: ${formatCents(order.taxCents)}`,
    `Total: ${formatCents(order.totalCents)}`,
    "",
    `Shipping to: ${order.shippingAddress}`,
    "",
    "This is a demo store. Nothing was charged.",
  ].join("\n");
}

export function orderEmailHtml(order: OrderEmail): string {
  const rows = order.lines
    .map(
      (line) =>
        `<tr><td style="padding:6px 0">${escapeHtml(line.title)} &times; ${line.quantity}</td><td style="padding:6px 0;text-align:right">${formatCents(line.priceCents * line.quantity)}</td></tr>`,
    )
    .join("");
  return `<div style="font-family:system-ui,sans-serif;max-width:520px;margin:auto;color:#18181b">
<h1 style="font-size:20px">Thanks for your order, ${escapeHtml(order.customerName)}</h1>
<p>Order <strong>${escapeHtml(order.orderNumber)}</strong></p>
<table style="width:100%;border-collapse:collapse;border-top:1px solid #e4e4e7">${rows}</table>
<p style="text-align:right;margin:8px 0">Subtotal ${formatCents(order.subtotalCents)}<br>Tax ${formatCents(order.taxCents)}<br><strong>Total ${formatCents(order.totalCents)}</strong></p>
<p style="color:#52525b">Shipping to: ${escapeHtml(order.shippingAddress)}</p>
<p style="color:#a1a1aa;font-size:12px">This is a demo store. Nothing was charged.</p>
</div>`;
}

export async function sendOrderConfirmation(order: OrderEmail): Promise<{ sent: boolean; reason?: string }> {
  const key = process.env.RESEND_API_KEY?.trim();
  if (!key) return { sent: false, reason: "RESEND_API_KEY is not set" };
  const from = process.env.EMAIL_FROM?.trim() || `${SITE_NAME} <onboarding@resend.dev>`;
  try {
    const response = await fetch("https://api.resend.com/emails", {
      method: "POST",
      headers: { authorization: `Bearer ${key}`, "content-type": "application/json" },
      body: JSON.stringify({
        from,
        to: [order.to],
        subject: `Your ${SITE_NAME} order ${order.orderNumber}`,
        html: orderEmailHtml(order),
        text: orderEmailText(order),
      }),
      signal: AbortSignal.timeout(8000),
    });
    if (!response.ok) return { sent: false, reason: `Resend answered ${response.status}` };
    return { sent: true };
  } catch (error) {
    return { sent: false, reason: error instanceof Error ? error.message : "request failed" };
  }
}
