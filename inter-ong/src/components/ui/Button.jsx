export default function Button({
  children,
  variant = 'primary',
  size = 'md',
  disabled = false,
  className = '',
  type = 'button',
  ...props
}) {
  const base =
    'inline-flex items-center justify-center font-bold rounded-xl transition-all duration-200 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer focus:outline-none focus:ring-2 focus:ring-offset-2'

  const sizes = {
    sm: 'px-3 py-1.5 text-xs',
    md: 'px-4 py-2.5 text-sm',
    lg: 'px-6 py-3.5 text-base',
  }

  const variants = {
    primary:
      'bg-gradient-to-r from-[#fb2782] to-[#ff4785] text-white shadow-md shadow-pink-500/25 hover:opacity-95 focus:ring-pink-500',
    secondary:
      'bg-pink-50 text-[#fb2782] dark:bg-pink-950/60 dark:text-pink-300 hover:bg-pink-100 dark:hover:bg-pink-900/60 focus:ring-pink-400',
    outline:
      'border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 focus:ring-slate-400',
    danger:
      'bg-red-600 hover:bg-red-700 text-white shadow-sm focus:ring-red-500',
  }

  return (
    <button
      type={type}
      disabled={disabled}
      className={`${base} ${sizes[size] || sizes.md} ${variants[variant] || variants.primary} ${className}`}
      {...props}
    >
      {children}
    </button>
  )
}
