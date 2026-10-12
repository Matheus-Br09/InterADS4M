export default function Card({ children, className = '', ...props }) {
  return (
    <div
      className={`rounded-3xl bg-white dark:bg-slate-900 p-6 sm:p-8 shadow-sm border border-pink-100/80 dark:border-slate-800 text-slate-800 dark:text-slate-100 ${className}`}
      {...props}
    >
      {children}
    </div>
  )
}
