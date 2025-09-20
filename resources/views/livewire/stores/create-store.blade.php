<div>
    <form wire:submit="create">
        {{ $this->form }}

        <div class="mt-5">

            {{-- Filament Action mit Bestätigungs-Dialog für Abbruch --}}
            <flux:button
                variant="primary"
                color="red"
                class=""
                wire:click="mountAction('cancel')"
            >
                Abbruch
            </flux:button>


            <flux:button variant="primary" color="green"
                         class="ms-3"
                         type="submit">Speichern
            </flux:button>
        </div>
    </form>

    <x-filament-actions::modals/>
</div>
