<div class="p-2 bg-light fixed-bottom">
<button type="submit" class="btn btn-primary mr-3"><i class="fas fa-tools mr-2"></i>{{$helper->t('save_and_continue_edit')}}</button>

    <button type="submit" class="btn btn-success" onclick="form.return_to_the_list.value=1;"><i class="fas fa-list-alt mr-2"></i>{{$helper->t('save_and_return_to_the_list')}}</button>

    <a href="#" class="ml-3 text-secondary" onclick="jQuery('.fixed-bottom').removeClass();jQuery(this).hide();return false;"><i class="fas fa-times-circle" title="Hide the buttons"></i></a>
</div>
