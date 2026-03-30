<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
            <h1>Painel Administrador</h1>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Mensagem de Sucesso -->
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <!-- UPLOAD FORM -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 border-b border-gray-200">
                    <h3 class="text-lg font-medium mb-4">Adicionar Nova Foto ao Feed</h3>
                    
                    <form action="{{ route('photos.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div>
                            <label for="image" class="block text-sm font-medium text-gray-700">Imagem da Foto (obrigatório)</label>
                            <!-- Tailwind form classes + border radius -->
                            <input type="file" name="image" id="image" required accept="image/*" class="mt-1 block w-full text-sm text-slate-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-full file:border-0
                                file:text-sm file:font-semibold
                                file:bg-indigo-50 file:text-indigo-700
                                hover:file:bg-indigo-100 border border-gray-300 rounded-md p-2">
                            @error('image')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Descrição/Comentário</label>
                            <textarea name="description" id="description" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Opcional. Ex: Nossa nova coleção de primavera!"></textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <button type="submit" class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                                Fazer Upload
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- GALERIA DAS FOTOS ADICIONADAS -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 border-b border-gray-200">
                    <h3 class="text-lg font-medium mb-4">Fotos Adicionadas ({{ $photos->count() }})</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        @if($photos->isEmpty())
                            <div class="col-span-full py-8 text-center text-gray-500">
                                <p>Nenhuma foto foi adicionada ao feed ainda.</p>
                                <p class="text-sm mt-1">Utilize o formulário acima para enviar a primeira foto.</p>
                            </div>
                        @else
                            @foreach($photos as $photo)
                                <div class="border border-gray-200 rounded-lg overflow-hidden shadow-sm relative flex flex-col bg-gray-50">
                                    <!-- URL to public storage -->
                                    <img src="{{ Storage::url($photo->image_path) }}" alt="{{ $photo->description }}" class="w-full h-48 object-cover">
                                    
                                    <div class="p-4 flex-1 flex flex-col justify-between">
                                        <p class="text-sm text-gray-700 mb-4 break-words">
                                            {{ $photo->description ?: 'Sem descrição' }}
                                        </p>
                                        
                                        <form action="{{ route('photos.destroy', $photo->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir esta foto do feed?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full text-center text-sm font-medium text-red-600 hover:text-white border border-red-500 hover:bg-red-600 py-1.5 rounded transition">
                                                Excluir Foto
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
