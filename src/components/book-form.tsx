"use client";

import Link from "next/link";
import { useActionState } from "react";
import { EMPTY_FORM, type FormState } from "@/lib/form-state";

export type BookFormValues = {
  isbn: string;
  title: string;
  author: string;
  price: string;
  stock: string;
  categoryId: string;
  description: string;
  coverImage: string;
};

/** Add and edit share this form. `action` is bound to the book id when editing. */
export function BookForm({
  action,
  initial,
  categories,
  editing,
}: {
  action: (previous: FormState, data: FormData) => Promise<FormState>;
  initial: BookFormValues;
  categories: { id: number; name: string }[];
  editing: boolean;
}) {
  const [state, formAction, pending] = useActionState(action, EMPTY_FORM);
  const v = { ...initial, ...state.values };

  return (
    <div className="admin-card">
      <div className="admin-card-body">
        <form action={formAction}>
          {state.errors.length > 0 ? (
            <div role="alert" className="admin-card admin-mb-6" style={{ borderLeft: "4px solid var(--admin-danger)" }}>
              <div className="admin-card-body">
                <ul style={{ margin: 0, paddingLeft: "1.1rem" }}>
                  {state.errors.map((error) => (
                    <li key={error}>{error}</li>
                  ))}
                </ul>
              </div>
            </div>
          ) : null}

          <div className="admin-grid admin-grid-cols-2">
            <div className="admin-form-group">
              <label htmlFor="isbn" className="admin-label">
                ISBN *
              </label>
              <input type="text" id="isbn" name="isbn" className="admin-input" defaultValue={v.isbn} required readOnly={editing} inputMode="numeric" />
              {editing ? <small className="admin-text-secondary" style={{ fontSize: "var(--admin-font-size-xs)", display: "block", marginTop: "0.5rem" }}>The ISBN identifies the book and cannot be changed.</small> : null}
            </div>
            <div className="admin-form-group">
              <label htmlFor="categoryId" className="admin-label">
                Category *
              </label>
              <select id="categoryId" name="categoryId" className="admin-select" defaultValue={v.categoryId} required>
                <option value="">Select Category</option>
                {categories.map((category) => (
                  <option key={category.id} value={category.id}>
                    {category.name}
                  </option>
                ))}
              </select>
            </div>
          </div>

          <div className="admin-form-group">
            <label htmlFor="title" className="admin-label">
              Title *
            </label>
            <input type="text" id="title" name="title" className="admin-input" defaultValue={v.title} required maxLength={255} />
          </div>
          <div className="admin-form-group">
            <label htmlFor="author" className="admin-label">
              Author *
            </label>
            <input type="text" id="author" name="author" className="admin-input" defaultValue={v.author} required maxLength={255} />
          </div>

          <div className="admin-grid admin-grid-cols-2">
            <div className="admin-form-group">
              <label htmlFor="price" className="admin-label">
                Price (USD) *
              </label>
              <input type="text" id="price" name="price" className="admin-input" defaultValue={v.price} required inputMode="decimal" placeholder="12.99" />
            </div>
            <div className="admin-form-group">
              <label htmlFor="stock" className="admin-label">
                Stock Quantity *
              </label>
              <input type="number" id="stock" name="stock" className="admin-input" min={0} max={100000} defaultValue={v.stock} required />
            </div>
          </div>

          <div className="admin-form-group">
            <label htmlFor="description" className="admin-label">
              Description
            </label>
            <textarea id="description" name="description" className="admin-textarea" rows={5} defaultValue={v.description} maxLength={5000} />
          </div>

          <div className="admin-form-group">
            <label htmlFor="coverImage" className="admin-label">
              Cover Image URL
            </label>
            <input type="url" id="coverImage" name="coverImage" className="admin-input" defaultValue={v.coverImage} placeholder="https://example.com/book-cover.jpg" maxLength={500} />
            <small className="admin-text-secondary" style={{ fontSize: "var(--admin-font-size-xs)", display: "block", marginTop: "0.5rem" }}>
              Enter the direct link to the cover image. Leave it empty to show a plain tile.
            </small>
          </div>

          <div className="admin-flex admin-flex-gap-4" style={{ marginTop: "2rem" }}>
            <button type="submit" className="admin-btn admin-btn-primary" disabled={pending} aria-busy={pending}>
              {pending ? "Saving..." : editing ? "Update Book" : "Add Book"}
            </button>
            <Link href="/admin/books" className="admin-btn admin-btn-secondary">
              Cancel
            </Link>
          </div>
        </form>
      </div>
    </div>
  );
}
