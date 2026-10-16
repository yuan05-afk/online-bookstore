import { ICON_VIEWBOX, ICONS } from "./icon-data";

/** Inline Solar icon. The markup comes from a generated file in this repo, never from user input. */
export function Icon({
  name,
  width = 20,
  className,
  style,
  title,
}: {
  name: keyof typeof ICONS | (string & {});
  width?: number;
  className?: string;
  style?: React.CSSProperties;
  title?: string;
}) {
  const body = ICONS[name];
  if (!body) return null;
  return (
    <svg
      viewBox={ICON_VIEWBOX}
      width={width}
      height={width}
      className={className}
      style={style}
      role={title ? "img" : undefined}
      aria-label={title}
      aria-hidden={title ? undefined : true}
      focusable="false"
      dangerouslySetInnerHTML={{ __html: body }}
      fill="none"
    />
  );
}
