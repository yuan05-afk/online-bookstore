import type { Metadata } from "next";
import Link from "next/link";
import { BookForm } from "@/components/book-form";
import { Icon } from "@/components/icon";
import { createBookAction } from "@/server/admin-actions";
import { listCategories } from "@/server/queries";

export const metadata: Metadata = { title: "Add New Book" };

export default async function NewBookPage() {
  const categories = await listCategories();
  return (
    <>
      <Link href="/admin/books" className="admin-back-link">
        <Icon name="arrow-left-linear" width={18} />
        Back to Books
      </Link>
      <h1 className="admin-page-title">Add New Book</h1>
      <BookForm
        action={createBookAction}
        editing={false}
        categories={categories}
        initial={{ isbn: "", title: "", author: "", price: "", stock: "0", categoryId: "", description: "", coverImage: "" }}
      />
    </>
  );
}
