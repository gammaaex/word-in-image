<?php
/**
 * @var \App\View\AppView $this
 */

echo $this->Form->create(null, ['enctype' => 'multipart/form-data']);
echo $this->Form->file('tweet_data');
echo $this->Form->button(__('Submit'));
echo $this->Form->end();