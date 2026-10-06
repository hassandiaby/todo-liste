<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ma To-Do List</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-indigo-50 min-h-screen flex justify-center p-4 pt-10">
<div class="w-full max-w-lg">

    <div class="flex items-center justify-between mb-4">
        <h1 class="text-3xl font-bold text-indigo-900">Ma To-Do List</h1>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="text-sm text-indigo-700 hover:underline">Se déconnecter</button>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-lg p-6">
        <p class="text-sm text-gray-500 mb-4">{{ $remaining }} tâche(s) à faire</p>

        @if(session('success'))
            <p class="bg-green-100 text-green-700 text-sm rounded-lg px-3 py-2 mb-4">{{ session('success') }}</p>
        @endif

        <form action="{{ route('tasks.store') }}" method="POST" class="flex flex-wrap gap-2 mb-2">
            @csrf
            <input type="text" name="title" placeholder="Nouvelle tâche..." required
                   class="flex-1 min-w-40 border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">
            <input type="date" name="due_date"
                   class="border border-gray-300 rounded-lg px-2 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400">
            <button class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-4 py-2 rounded-lg">Ajouter</button>
        </form>

        @error('title')<p class="text-red-500 text-sm mb-2">{{ $message }}</p>@enderror

        <div class="flex gap-2 my-4 text-sm">
            <a href="{{ route('tasks.index') }}"
               class="px-3 py-1 rounded-full {{ !$filter ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">Toutes</a>
            <a href="{{ route('tasks.index', ['filter' => 'todo']) }}"
               class="px-3 py-1 rounded-full {{ $filter === 'todo' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">À faire</a>
            <a href="{{ route('tasks.index', ['filter' => 'done']) }}"
               class="px-3 py-1 rounded-full {{ $filter === 'done' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">Faites</a>
        </div>

        <ul class="space-y-2">
            @forelse($tasks as $task)
            <li class="flex items-center gap-2 border border-gray-200 rounded-lg px-3 py-2 hover:bg-gray-50">
                <form action="{{ route('tasks.update', $task) }}" method="POST">
                    @csrf @method('PATCH')
                    <button title="Marquer comme faite" class="text-xl text-indigo-600">{{ $task->is_done ? '☑' : '☐' }}</button>
                </form>

                <form action="{{ route('tasks.rename', $task) }}" method="POST" class="flex flex-1 items-center gap-2">
                    @csrf @method('PUT')
                    <input type="text" name="title" value="{{ $task->title }}" readonly
                           class="flex-1 bg-transparent border border-transparent rounded px-2 py-1 {{ $task->is_done ? 'line-through text-gray-400' : 'text-gray-800' }}">
                    <button type="button" class="text-sm text-indigo-600 hover:underline" onclick="editTask(this)">Modifier</button>
                    <button class="text-sm text-green-600 hover:underline hidden">Enregistrer</button>
                </form>

                @if($task->due_date)
                    <span class="text-xs px-2 py-1 rounded-full {{ !$task->is_done && $task->due_date->isPast() ? 'bg-red-100 text-red-600' : 'bg-gray-100 text-gray-500' }}">
                        {{ $task->due_date->format('d/m/Y') }}
                    </span>
                @endif

                <form action="{{ route('tasks.destroy', $task) }}" method="POST">
                    @csrf @method('DELETE')
                    <button class="text-gray-400 hover:text-red-500" title="Supprimer">✕</button>
                </form>
            </li>
            @empty
                <li class="text-center text-gray-400 py-6">Aucune tâche pour le moment.</li>
            @endforelse
        </ul>
    </div>
</div>

<script>
function editTask(btn) {
    const form = btn.closest('form');
    const input = form.querySelector('input[name="title"]');
    input.readOnly = false;
    input.classList.replace('border-transparent', 'border-gray-300');
    input.focus();
    btn.classList.add('hidden');
    form.querySelector('button:not([type="button"])').classList.remove('hidden');
}
</script>
</body>
</html>