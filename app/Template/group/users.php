<section id="main">
    <div class="page-header">
        <ul class="btn-group">
            <li><?= $this->url->buttonLink('<fa-users>' . t('View all groups'), 'group', 'index') ?></li>
            <li><?= $this->url->buttonLink('<fa-plus>' . t('Add group member'), 'group', 'associate', array('group_id' => $group['id'])) ?></li>
        </ul>
    </div>
    <?php if ($paginator->isEmpty()): ?>
        <p class="alert"><?= t('There is no user in this group.') ?></p>
    <?php else: ?>
        <table>
            <tr>
                <th><?= $paginator->order(t('Id'), 'id') ?></th>
                <th><?= $paginator->order(t('Username'), 'username') ?></th>
                <th><?= $paginator->order(t('Name'), 'name') ?></th>
                <th><?= $paginator->order(t('Email'), 'email') ?></th>
                <th><?= t('Actions') ?></th>
            </tr>
            <?php foreach ($paginator->getCollection() as $user): ?>
            <tr>
                <td>
                    <?= $this->url->link('#'.$user['id'], 'user', 'show', array('user_id' => $user['id'])) ?>
                </td>
                <td>
                    <?= $this->url->link($this->text->e($user['username']), 'user', 'show', array('user_id' => $user['id'])) ?>
                </td>
                <td>
                    <?= $this->text->e($user['name']) ?>
                </td>
                <td>
                    <a href="mailto:<?= $this->text->e($user['email']) ?>"><?= $this->text->e($user['email']) ?></a>
                </td>
                <td>
                    <?= $this->url->link(t('Remove this user'), 'group', 'dissociate', array('group_id' => $group['id'], 'user_id' => $user['id'])) ?>
                </td>
            </tr>
            <?php endforeach ?>
        </table>

        <?= $paginator ?>
    <?php endif ?>
</section>
