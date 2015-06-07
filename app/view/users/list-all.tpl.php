<h3><?= $title ?></h3>
<br/><br/>
<div class="user-wrapper">

    <?php foreach ($users as $user) : ?>
        <?php $url = $this->url->create('comment/view-by-user/' . $user->id); ?>

        
        <div class="user-individual">
            
            <a href="<?= $this->url->create('comment/view-by-user/' . $user->id) ?>">

                <div class="user-avatar">
                    <img src="<?= getGravatar($user->email, 80) ?>">
                </div>
                <br/><br/>
                <div class="user-info">

                    <table>
                        <tr>
                            <td width="147px">Display Name</td>
                            <td width="2px">:</td>
                            <td class="right-align"> <?= $user->name ?></td>
                        </tr>
                        <tr>
                            <td width="147px">Email</td>
                            <td width="2px">:</td>
                            <td class="right-align"> <?= $user->email ?></td>
                        </tr>
                    </table>

                </div>
                
            </a>
            
        </div>
    <hr class="user-seperator">
    <?php endforeach; ?>

</div>