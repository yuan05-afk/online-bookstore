import type { Metadata } from "next";
import Link from "next/link";
import { redirect } from "next/navigation";
import { BookForm } from "@/components/book-form";
import { Icon } from "@/components/icon";
import { centsToInput } from "@/lib/money";
import { withNotice } from "@/lib/notices";
import { updateBookAction } from "@/server/admin-actions";
import { getBook, listCategories } from "@/server/queries";

export const metadata: Metadata = { title: "Edit Book" };

export default async function EditBookPage({ params }: { params: Promise<{ id: string }> }) {
  const id = Number((await params).id);
  const [book, categories] = await Promise.all([getBook(id), listCategories()]);
  if (!book) redirect(withNotice("/admin/books", "book_not_found"));

  return (
    <>
      <Link href="/admin/books" className="admin-back-link">
        <Icon name="arrow-left-linear" width={18} />
        Back to Books
      </Link>
      <h1 className="admin-page-title">Edit Book</h1>
      <BookForm
        action={updateBookAction.bind(null, book.id)}
        editing
        categories={categories}
        initial={{
          isbn: book.isbn,
          title: book.title,
          author: book.author,
          price: centsToInput(book.priceCents),
          stock: String(book.stock),
          categoryId: book.categoryId ? String(book.categoryId) : "",
          description: book.description ?? "",
          coverImage: book.coverImage ?? "",
        }}
      />
    </>
  );
}
