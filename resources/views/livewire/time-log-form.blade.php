<div class="max-w-3xl mx-auto p-6 bg-white rounded-lg shadow-lg">
    @if (session()->has('message'))
        <div class="mt-4 p-4 bg-green-100 text-green-700 rounded">
            {{ session('message') }}
        </div>
    @endif
    <h1 class="text-2xl font-semibold text-gray-800 mb-4">Work Hours / Create</h1>
    <form wire:submit.prevent="submit">
        {{ $this->form }}

        <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded mt-4 hover:bg-blue-700">
            Submit
        </button>
    </form>
</div>