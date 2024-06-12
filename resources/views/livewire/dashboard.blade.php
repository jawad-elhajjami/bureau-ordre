<div class="py-12">
    <div class="overflow-hidden">
        <div class="relative flex items-center justify-center">
            <x-mary-loading class="text-primary loading-lg absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 h-screen" wire:loading />
        </div>

        <x-mary-header title="Tableau de bord" subtitle="Analyses et statistiques"/>

        <div class="grid lg:grid-cols-4 gap-5 lg:gap-8 mb-10 md:grid-cols-3">
            <x-mary-stat
                title="Documents échangés"
                :value="$documentStats['documents'] ?? 0"
                icon="o-paper-clip"
            />
            <x-mary-stat
                title="Catégories des documents"
                :value="$documentStats['categories'] ?? 0"
                icon="o-folder"
            />
            <x-mary-stat
                title="Nombre du personnel"
                :value="$documentStats['users'] ?? 0"
                icon="o-users"
            />
            <x-mary-stat
                title="Nombre du départements"
                :value="$documentStats['services'] ?? 0"
                icon="o-building-office-2"
            />
        </div>

        <div class="grid lg:grid-cols-5 gap-8 mt-8">
            <x-mary-card class="col-span-5 lg:col-span-3" :title="$documentStats['documents'] ?? 0" subtitle="Documents échangés pendant cette année" shadow separator>
                <canvas id="myChart"></canvas>
            </x-mary-card>

            <x-mary-card class="col-span-5 lg:col-span-2" shadow separator>
                <div class="flex items-center justify-between pb-5">
                    <h1 class="text-base font-medium w-full">Nombre de documents par catégorie</h1>
                    <x-mary-button class="btn-primary" label="Voir plus" link="{{ route('manage-categories') }}" wire:navigate/>
                </div>
                <ul>
                    @foreach($documentsByCategory as $category)
                        <li class="flex items-center justify-between py-3 border-b-2">
                            <p class="text-sm md:w-3/4">{{ $category->category_name }}</p>
                            <span class="text-[#9E9E9E]">{{ $category->documents_count }}</span>
                        </li>
                    @endforeach
                </ul>
            </x-mary-card>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('livewire:navigated', function () {
        const ctx = document.getElementById('myChart').getContext('2d');
        const documentsPerMonth = @json($documentsPerMonth);

        // Create labels for the last 12 months
        const labels = Object.keys(documentsPerMonth).map(month => {
            const date = new Date(month + '-01');
            return date.toLocaleString('default', { month: 'long', year: 'numeric' });
        }).reverse();

        // Create data array for the chart
        const data = Object.values(documentsPerMonth).reverse();

        const myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Documents échangés',
                    data: data,
                    backgroundColor: 'rgba(72, 43, 217, .1)',
                    borderColor: 'rgba(72, 43, 217, 1)',
                    borderWidth: 1,
                    pointRadius: 5,
                    pointBackgroundColor: 'white',
                    pointBorderColor: 'rgba(255, 99, 132, 1)',
                    pointHoverRadius: 8,
                    pointHoverBackgroundColor: 'white',
                    pointHoverBorderColor: 'rgba(255, 99, 132, 1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        align: 'start',
                        labels: {
                            boxWidth: 20,
                            padding: 20
                        }
                    }
                },
                layout: {
                    padding: {
                        bottom: 40
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    });
</script>
