<style>
    {!! \Arqon\Forms\ArqonFormsServiceProvider::getDistCss() !!}
</style>

<div class="py-2">
    <livewire:arqon-forms.public-form :form-id="$form->id" :preview="true" />
</div>
