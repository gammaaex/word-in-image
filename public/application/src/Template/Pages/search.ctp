<?php
/** @var string $status */
/** @var string $queryWord */
/** @var \App\Model\Document\Tweet[] $tweets */
?>

<?php if(is_null($status)): ?>
<div class="form large-9 medium-8 columns content">
    <?= $this->Form->create(null, ['type' => 'get']) ?>
    <fieldset>
        <legend><?= __('Search in Images') ?></legend>
        <?php
        echo $this->Form->control('query');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Search')) ?>
    <?= $this->Form->end() ?>
</div>

<?php else: ?>
<div class=" large-9 medium-8 columns content">
    <h3><?= __("Search Result By '${queryWord}'") ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
        <tr>
            <th scope="col"><?= $this->Paginator->sort('text') ?></th>
            <th scope="col"><?= $this->Paginator->sort('tweet_url') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($tweets as $tweet): ?>
            <tr>
                <td><?= h($tweet->text) ?></td>
                <td><a href="<?= h($tweet->tweet_url)?>" target="_blank"><?= h($tweet->tweet_url) ?></a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif ?>