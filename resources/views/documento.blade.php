<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 mb-6">Analizar Documento</h2>
            
            <input type="file" id="archivoInput" style="display: none;" accept=".txt,.json,.csv" />

            <button type="button" id="btnCargar" class="btn-rojo bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Cargar documento</button>

            <div id="resultadoIA" class="text-gray-700 dark:text-gray-300" style="margin-top: 20px; white-space: pre-wrap;"></div>
        </div>
    </div>

    @stack('scripts')
    <script>
        const btnCargar = document.getElementById('btnCargar');
        const archivoInput = document.getElementById('archivoInput');
        const resultadoDiv = document.getElementById('resultadoIA');

        // Vincular botón visible al input oculto
        btnCargar.addEventListener('click', () => {
            archivoInput.click();
        });

        // Detectar selección de archivo y subir automáticamente
        archivoInput.addEventListener('change', async (event) => {
            const file = event.target.files[0];
            if (!file) return;

            resultadoDiv.innerText = "Subiendo y analizando con la IA... por favor espera.";

            // Preparamos los datos para enviar al controlador de Laravel
            const formData = new FormData();
            formData.append('documento', file);

            try {
                // Hacemos la petición a la ruta que creamos en web.php
                const response = await fetch('{{ route("documento.parsear") }}', {
                    method: 'POST',
                    headers: {
                        // Laravel exige este token para todas las peticiones POST vía AJAX
                        'X-CSRF-TOKEN': '{{ csrf_token() }}' 
                    },
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    resultadoDiv.innerText = data.resultado; // Mostramos lo que respondió Llama 3
                } else {
                    resultadoDiv.innerText = "Error: " + data.message;
                }

            } catch (error) {
                console.error("Error en la petición:", error);
                resultadoDiv.innerText = "Hubo un error de conexión con el servidor.";
            } finally {
                // Limpiamos el input por si quiere subir el mismo archivo otra vez
                archivoInput.value = '';
            }
        });
    </script>
</x-app-layout>
