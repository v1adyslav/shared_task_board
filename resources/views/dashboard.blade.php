<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Shared Task Board') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div id="task-board-app"></div>
            </div>
        </div>
    </div>

    <script>
        window.taskBoardConfig = {{ Illuminate\Support\Js::from([
            'board' => $board,
            'routes' => [
                'board' => route('boards.show', ['board' => data_get($board, 'id')]),
                'store' => route('cards.store'),
                'updateTemplate' => url('/cards/__CARD__'),
                'moveTemplate' => url('/cards/__CARD__/move'),
                'deleteTemplate' => url('/cards/__CARD__'),
            ],
        ]) }}
    </script>
</x-app-layout>
