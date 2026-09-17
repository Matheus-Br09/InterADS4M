import "./css/Contato.css"

export default function Contato() {
  return (
    <div className="min-h-screen flex items-center justify-center 
    bg-gradient-to-b from-pink-200 via-pink-100 to-white p-6">

      <main className="flex flex-col lg:flex-row w-full max-w-6xl 
      bg-gradient-to-r from-pink-400 to-rose-400 
      rounded-3xl p-8 gap-10 shadow-xl">

        {/* CARD DE CONTATO */}
        <div className="w-[510px] bg-white rounded-3xl p-6 shadow-md space-y-5">

          <div className="bg-gray-50 rounded-xl p-4 hover:shadow-md transition">
            <p className="text-sm text-gray-400">Email da empresa</p>
            <p className="font-semibold text-gray-800">sostudopelosocial@gmail.com</p>
          </div>

          <div className="bg-gray-50 rounded-xl p-4 hover:shadow-md transition">
            <p className="text-sm text-gray-400">Número de Telefone</p>
            <p className="font-semibold text-gray-800">(81) 99281-2080</p>
          </div>

          <div className="bg-gray-50 rounded-xl p-4 hover:shadow-md transition">
            <p className="text-sm text-gray-400">Endereço</p>
            <p className="font-semibold text-gray-800">
              Rua Hosana Alves do Nascimento, nº 493
            </p>
            <p className="text-gray-700">Casa 01 - Janga</p>
            <p className="text-gray-700">Paulista / PE</p>
          </div>

          <div className="bg-gray-50 rounded-xl p-4 hover:shadow-md transition">
            <p className="text-sm text-gray-400">Instagram</p>
            <p className="font-semibold text-pink-600">@SosTudopelosocial</p>
          </div>

        </div>

        {/* MAPA */}
        <div className="flex-1">
          <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3951.5527711809514!2d-34.83390742411175!3d-7.941686879106385!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x7ab3d11fee5da57%3A0x7e7b6c1816d65ae6!2sSOS%20TUDO%20PELO%20SOCIAL!5e0!3m2!1spt-BR!2sbr!4v1789083824825!5m2!1spt-BR!2sbr"
            className="w-full h-[490px] rounded-3xl shadow-lg border border-white"
            loading="lazy"
          ></iframe>
        </div>

      </main>
    </div>
  )
}