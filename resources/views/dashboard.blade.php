<x-app-layout>
    <head>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    </head>
    <div class="py-12">
        <div class="overflow-hidden">
            <div class="relative flex items-center justify-center">
                <x-mary-loading class="text-primary loading-lg absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 h-screen" wire:loading />
            </div>

            <x-mary-header title="Tableau de bord" subtitle="Analyses et statistiques"/>

            <div class="grid lg:grid-cols-4 gap-5 lg:gap-8 mb-10 md:grid-cols-3">
                <x-mary-stat
                    title="Documents échangés"
                    description="12%"
                    value="513"
                    icon="o-paper-clip"
                />
                <x-mary-stat
                    title="Catégories des documents"
                    value="12"
                    icon="o-folder"
                />
                <x-mary-stat
                    title="Nombre du personnel"
                    value="34"
                    icon="o-users"
                />
                <x-mary-stat
                    title="Nombre du départements"
                    value="22.124"
                    icon="o-building-office-2"
                />
            </div>

            <div class="grid lg:grid-cols-5 gap-8 mt-8">
                <x-mary-card class="col-span-5 lg:col-span-3" title="513" subtitle="Docments échangés pendant cette année" shadow separator>
                    <canvas id="myChart"></canvas>
                </x-mary-card>

                <x-mary-card class="col-span-5 lg:col-span-2" shadow separator>
                    <div class="flex items-center justify-between pb-5">
                        <h1 class="text-base font-medium w-full">Nombre de documents par catégorie</h1>
                        <x-mary-button class="btn-primary" label="Voir plus" link="{{ route('manage-categories') }}" wire:navigate/>
                    </div>
                    <ul>
                        <li class="flex items-center justify-between py-3 border-b-2">
                            <p class="text-sm md:w-3/4">Rapports d'activité et bilans</p>
                            <span class="text-[#9E9E9E]">20</span>
                        </li>
                        <li class="flex items-center justify-between py-3 border-b-2">
                            <p class="text-sm md:w-3/4">Factures et bons de commande</p>
                            <span class="text-[#9E9E9E]">15</span>
                        </li>
                        <li class="flex items-center justify-between py-3 border-b-2">
                            <p class="text-sm md:w-3/4">Procès-verbaux de réunions</p>
                            <span class="text-[#9E9E9E]">31</span>
                        </li>
                        <li class="flex items-center justify-between py-3 border-b-2">
                            <p class="text-sm md:w-3/4">Actes administratifs</p>
                            <span class="text-[#9E9E9E]">60</span>
                        </li>
                        <li class="flex items-center justify-between py-3 border-b-2">
                            <p class="text-sm md:w-3/4">Dossiers et fichiers du personnel</p>
                            <span class="text-[#9E9E9E]">18</span>
                        </li>
                        <li class="flex items-center justify-between py-3 border-b-2">
                            <p class="text-sm md:w-3/4">Registres de présence</p>
                            <span class="text-[#9E9E9E]">10</span>
                        </li>
                        <li class="flex items-center justify-between py-3 border-b-2">
                            <p class="text-sm md:w-3/4">Évaluations de performance</p>
                            <span class="text-[#9E9E9E]">05</span>
                        </li>
                    </ul>
                </x-mary-card>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('livewire:navigated', function () {
            const ctx = document.getElementById('myChart').getContext('2d');
            const myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
                    datasets: [{
                        label: 'Documents échangés',
                        data: [12, 19, 3, 5, 2, 3, 10],
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
                        drawOnChartArea: true,
                        padding: {
                            bottom: 40
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

        });
    </script>

</x-app-layout>
