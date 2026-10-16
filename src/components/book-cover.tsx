"use client";

import { useState } from "react";

/**
 * Cover image with a graceful fallback. The covers are links to other sites, which can move or
 * disappear, so a broken one shows the same "No Image" tile the store uses when there is no link.
 */
export function BookCover({ src, title, placeholderText = "No Image" }: { src: string | null; title: string; placeholderText?: string }) {
  const [failed, setFailed] = useState(false);
  if (!src || failed) return <div className="user-book-placeholder">{placeholderText}</div>;
  return (
    // eslint-disable-next-line @next/next/no-img-element -- remote covers on many hosts; sizing is handled by the stylesheet
    <img src={src} alt={title} loading="lazy" decoding="async" referrerPolicy="no-referrer" onError={() => setFailed(true)} />
  );
}
