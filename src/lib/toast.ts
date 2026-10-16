/** A short message in the corner. Uses the `.notification` styles from user.css. Browser only. */
export function toast(message: string, type: "success" | "error" = "success") {
  if (typeof document === "undefined") return;
  const element = document.createElement("div");
  element.className = `notification notification-${type}`;
  element.setAttribute("role", type === "error" ? "alert" : "status");
  element.textContent = message;
  document.body.appendChild(element);
  window.setTimeout(() => element.classList.add("show"), 10);
  window.setTimeout(() => {
    element.classList.remove("show");
    window.setTimeout(() => element.remove(), 300);
  }, 3000);
}
