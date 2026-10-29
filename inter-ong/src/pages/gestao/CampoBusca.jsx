export default function CampoBusca({ valor, onMudar, rotulo }) {
  return (
    <label className="mt-4 block text-sm font-semibold">
      {rotulo}
      <input type="search" value={valor} maxLength={100} onChange={(e) => onMudar(e.target.value)}
        className="mt-1 block w-full rounded-lg border border-slate-300 bg-white p-2 font-normal md:max-w-md" />
    </label>
  )
}
