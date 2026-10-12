export default function Input({
  label,
  id,
  error,
  helperText,
  className = '',
  required = false,
  ...props
}) {
  return (
    <div className="w-full space-y-1">
      {label && (
        <label htmlFor={id} className="block text-xs font-bold text-slate-700 dark:text-slate-300">
          {label} {required && <span className="text-pink-600">*</span>}
        </label>
      )}
      <input
        id={id}
        required={required}
        aria-invalid={Boolean(error)}
        aria-describedby={error ? `${id}-error` : helperText ? `${id}-helper` : undefined}
        className={`w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 p-3 text-sm text-slate-800 dark:text-slate-100 transition-colors focus:border-[#fb2782] focus:ring-2 focus:ring-pink-100 dark:focus:ring-pink-950/50 outline-none ${
          error ? 'border-red-500 dark:border-red-500' : ''
        } ${className}`}
        {...props}
      />
      {error && (
        <p id={`${id}-error`} className="text-xs text-red-600 dark:text-red-400 font-medium" role="alert">
          {error}
        </p>
      )}
      {!error && helperText && (
        <p id={`${id}-helper`} className="text-xs text-slate-500 dark:text-slate-400">
          {helperText}
        </p>
      )}
    </div>
  )
}
