/** What a form action hands back to its form: messages to show, and values to put back in the fields. */
export type FormState = {
  errors: string[];
  values: Record<string, string>;
  /** Set by actions that finish with a message instead of a redirect. */
  message?: string;
};

export const EMPTY_FORM: FormState = { errors: [], values: {} };
