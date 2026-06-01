<?php
if (Yii::app()->user->isAdmin || Yii::app()->user->isSu()) {
    $savedSelectedUsers = isset($savedSelectedUsers) && is_array($savedSelectedUsers) ? $savedSelectedUsers : array();
    $usersData = array();
    foreach ($users as $user) {
        $userId = (int) $user->id;
        $displayName = trim($user->imie . ' ' . $user->nazwisko);
        if ($displayName === '') {
            $displayName = $user->login;
        }
        $usersData[] = array(
            'id' => $userId,
            'displayName' => $displayName,
            'login' => $user->login,
            'email' => $user->email,
            'firstName' => $user->imie,
            'lastName' => $user->nazwisko,
            'search' => strtolower($displayName . ' ' . $user->login . ' ' . $user->email . ' ' . $userId),
        );
    }

    Yii::app()->clientScript->registerCss('guide-token-reset-ui', '
        body.page-reset-guide-tokens{min-height:100vh;display:flex;flex-direction:column}
        body.page-reset-guide-tokens #content{flex:1 0 auto}
        body.page-reset-guide-tokens #footer{margin-top:auto}
        .guide-token-reset-panel{margin:20px 0 24px;border:1px solid #d9dee5;border-radius:12px;background:#fff;box-shadow:0 6px 24px rgba(15,23,42,.06);overflow:hidden}
        .guide-token-reset-panel__header{padding:16px 18px;background:linear-gradient(180deg,#f8fbff 0%,#eef4fb 100%);border-bottom:1px solid #d9dee5}
        .guide-token-reset-panel__title{margin:0 0 4px;font-size:16px;font-weight:700;color:#17324d}
        .guide-token-reset-panel__subtitle{margin:0;color:#52616f;font-size:13px;line-height:1.5}
        .guide-token-flash{margin:12px 18px 0}
        .guide-token-reset-toolbar{display:flex;gap:10px;align-items:center;justify-content:space-between;padding:14px 18px;border-bottom:1px solid #e6ebf1;background:#fff;flex-wrap:wrap}
        .guide-token-reset-meta{font-size:13px;color:#52616f;white-space:nowrap}
        .guide-token-reset-action-button,.guide-token-reset-action-button:link,.guide-token-reset-action-button:visited{display:inline-flex;align-items:center;justify-content:center;padding:10px 14px;border-radius:8px;text-decoration:none;border:1px solid transparent;font-weight:700;font-size:13px;cursor:pointer}
        .guide-token-reset-action-button--secondary{background:#fff;border-color:#cfd8e3;color:#17324d}
        .guide-token-reset-action-button--primary{background:linear-gradient(180deg,#1377b2 0%,#0a5f93 100%);border-color:#0a5f93;color:#fff}
        .guide-token-dropdown-trigger-wrap{flex:1 1 320px;min-width:260px}
        .guide-token-dropdown-trigger{width:100%;justify-content:space-between;text-align:left}
        .guide-token-dropdown-trigger__count{font-size:12px;opacity:.85;margin-left:12px}
        .guide-token-dropdown-panel{display:none;margin:0 18px 18px;border:1px solid #d9dee5;border-radius:12px;background:#fff}
        .guide-token-dropdown-panel.is-open{display:block}
        .guide-token-dropdown-panel__header{padding:14px;border-bottom:1px solid #e6ebf1}
        .guide-token-reset-search input{width:100%;padding:11px 14px;border:1px solid #cfd8e3;border-radius:10px;font-size:14px;box-sizing:border-box}
        .guide-token-dropdown-list{max-height:260px;overflow:auto;padding:10px 12px}
        .guide-token-dropdown-item{display:flex;gap:10px;align-items:flex-start;padding:8px 10px;border-radius:10px;cursor:pointer}
        .guide-token-dropdown-item:hover{background:#f7fbff}
        .guide-token-dropdown-item + .guide-token-dropdown-item{margin-top:4px}
        .guide-token-dropdown-item__check{margin-top:2px}
        .guide-token-dropdown-item__body{flex:1 1 auto;min-width:0}
        .guide-token-dropdown-item__name{display:block;font-weight:700;color:#17324d;line-height:1.35}
        .guide-token-dropdown-item__meta{display:block;margin-top:2px;color:#66758a;font-size:12px;line-height:1.45;word-break:break-word}
        .guide-token-selected-section{margin:0 18px 18px}
        .guide-token-selected-section__header{display:flex;justify-content:space-between;gap:10px;align-items:baseline;margin:0 0 6px}
        .guide-token-selected-section__title{margin:0;font-size:15px;font-weight:700;color:#17324d}
        .guide-token-selected-section__count{font-size:13px;color:#52616f;white-space:nowrap}
        .guide-token-selected-section__note{margin:0 0 12px;font-size:12px;line-height:1.5;color:#66758a}
        .guide-token-selected-table-wrap{border:1px solid #D0E3EF;border-radius:10px;overflow:hidden;background:#fff}
        .guide-token-selected-table{width:100%;border-collapse:collapse;font-size:13px}
        .guide-token-selected-table th,.guide-token-selected-table td{border:1px solid #fff;padding:10px 8px;text-align:left;vertical-align:middle}
        .guide-token-selected-table th{background-color:#E9E9F4;color:#000;font-weight:700}
        .guide-token-selected-table tbody tr.even{background:#F8F8F8}
        .guide-token-selected-table tbody tr.odd{background:#E5F1F4}
        .guide-token-selected-table tbody tr:hover{background:#ECFBD4}
        .guide-token-selected-table__check{width:34px;text-align:center}
        .guide-token-selected-table__id{width:70px}
        .guide-token-selected-table__title{font-weight:700;color:#17324d;display:block}
        .guide-token-selected-table__meta{color:#66758a;font-size:12px}
        .guide-token-selected-table-empty{padding:18px;color:#637082;text-align:center;border-top:1px solid #edf2f7}
        .guide-token-footer-actions{display:flex;gap:10px;flex-wrap:wrap;margin:20px 0 0 18px}
        .guide-token-note{margin:14px 0 0 18px;font-size:12px;color:#66758a}
        .guide-token-remove-button{background:#fff;border-color:#cfd8e3;color:#17324d}
        .guide-token-download-button{background:#f4f7fb;border-color:#cfd8e3;color:#17324d}
        .guide-token-empty-state{padding:18px;color:#637082;text-align:center}
    ');

    Yii::app()->clientScript->registerScript('guide-token-bootstrap-data', 'window.guideTokenBootstrap = ' . CJavaScript::encode(array(
        'users' => $usersData,
        'selectedIds' => array_values($savedSelectedUsers),
        'ajaxUrl' => Yii::app()->createUrl('site/resetGuideTokensForSelectedUsers'),
    )) . ';', CClientScript::POS_BEGIN);

    Yii::app()->clientScript->registerScript('guide-token-reset-ui', "
        (function($){
            var users = window.guideTokenBootstrap.users || [];
            var selected = {};
            var actionChecked = {};
            var timer = null;
            $.each(window.guideTokenBootstrap.selectedIds || [], function(_, id){ selected[parseInt(id, 10)] = true; });
            function ids(){ var out=[]; $.each(users, function(_, u){ if (selected[u.id]) out.push(u.id); }); return out; }
            function actionIds(){ var out=[]; $.each(ids(), function(_, id){ if (actionChecked[id]) out.push(id); }); return out; }
            function esc(v){ return $('<div>').text(v == null ? '' : String(v)).html(); }
            function syncHidden(){ var html=''; $.each(ids(), function(_, id){ html += '<input type=\"hidden\" name=\"selectedUsers[]\" value=\"' + id + '\">'; }); $('#guideTokenHiddenSelectedInputs').html(html); }
            function syncActionHidden(){ var html=''; $.each(actionIds(), function(_, id){ html += '<input type=\"hidden\" name=\"actionSelectedUsers[]\" value=\"' + id + '\">'; }); $('#guideTokenActionSelectedInputs').html(html); }
            function syncMeta(){ var c=ids().length; $('#guideTokenDropdownCount').text(c + ' selected'); $('.guide-token-reset-meta').text(c + ' selected, ' + users.length + ' total'); $('#guideTokenSelectedSectionCount').text(c + ' selected'); }
            function renderDropdown(){ var q=$.trim($('#guideTokenUserSearch').val()).toLowerCase(), html=''; $.each(users, function(_, u){ if (q && u.search.indexOf(q) === -1) return; html += '<label class=\"guide-token-dropdown-item\"><input type=\"checkbox\" class=\"guide-token-dropdown-item__check\" data-id=\"' + u.id + '\"' + (selected[u.id] ? ' checked=\"checked\"' : '') + '><span class=\"guide-token-dropdown-item__body\"><span class=\"guide-token-dropdown-item__name\">' + esc(u.displayName) + '</span><span class=\"guide-token-dropdown-item__meta\">' + esc(u.login + ' / ' + u.email + ' / ID ' + u.id) + '</span></span></label>'; }); $('#guideTokenDropdownList').html(html || '<div class=\"guide-token-empty-state\">No users match the current search.</div>'); }
            function renderTable(){ var html=''; $.each(ids(), function(index, id){ var u=null; $.each(users, function(_, user){ if (user.id === id) { u=user; return false; } }); if (!u) return; html += '<tr class=\"' + (index % 2 === 0 ? 'even' : 'odd') + '\" data-id=\"' + u.id + '\"><td class=\"guide-token-selected-table__check\"><input type=\"checkbox\" class=\"guide-token-table-item-check\" value=\"' + u.id + '\"' + (actionChecked[u.id] ? ' checked=\"checked\"' : '') + '></td><td class=\"guide-token-selected-table__id\">' + esc(u.id) + '</td><td>' + esc(u.login) + '</td><td>' + esc(u.email) + '</td><td>' + esc(u.firstName || '') + '</td><td>' + esc(u.lastName || '') + '</td></tr>'; }); $('#guideTokenSelectedTableBody').html(html); $('#guideTokenSelectedTableEmpty').toggle(html === ''); syncTableSelectAll(); }
            function syncTableSelectAll(){ var current = ids(), checked = 0; $.each(current, function(_, id){ if (actionChecked[id]) checked++; }); $('#guideTokenTableSelectAll').prop('checked', current.length > 0 && checked === current.length).prop('indeterminate', checked > 0 && checked < current.length); }
            function showFlash(type, message){ var \$box = $('<div class=\"' + (type === 'success' ? 'flash-success' : 'flash-error') + ' guide-token-flash\"></div>').text(message); $('#guideTokenFlashArea').html(\$box); window.setTimeout(function(){ \$box.fadeOut(200, function(){ $(this).remove(); }); }, 3000); }
            function persist(){ window.clearTimeout(timer); timer = window.setTimeout(function(){ $.post(window.guideTokenBootstrap.ajaxUrl, { ajaxSaveSelection: 1, selectedUsers: ids() }); }, 100); }
            function refresh(){ syncHidden(); syncMeta(); renderDropdown(); renderTable(); }
            $(function(){ refresh(); $('#guideTokenDropdownToggle').on('click', function(e){ e.preventDefault(); $('#guideTokenDropdownPanel').toggleClass('is-open'); }); $('#guideTokenUserSearch').on('input', renderDropdown); $('#guideTokenDropdownList').on('change', '.guide-token-dropdown-item__check', function(){ var id=parseInt($(this).data('id'),10); if($(this).is(':checked')) selected[id]=true; else { delete selected[id]; delete actionChecked[id]; } refresh(); persist(); }); $('#guideTokenSelectedTableBody').on('change', '.guide-token-table-item-check', function(){ var id=parseInt($(this).val(),10); if($(this).is(':checked')) actionChecked[id]=true; else delete actionChecked[id]; syncTableSelectAll(); }); $('#guideTokenTableSelectAll').on('change', function(){ var checked = $(this).is(':checked'); $.each(ids(), function(_, id){ if (checked) actionChecked[id] = true; else delete actionChecked[id]; }); renderTable(); }); $('#guideTokenRemoveSelected').on('click', function(e){ e.preventDefault(); var action = actionIds(); if(!action.length){ showFlash('error', 'Please select at least one user in the table'); return; } if(!confirm('Are you sure you want to remove the selected users from the selected users table?')) return; $.each(action, function(_, id){ delete selected[id]; delete actionChecked[id]; }); refresh(); persist(); showFlash('success', 'Selected users removed.'); }); $('#guideTokenResetSubmit').on('click', function(){ var action = actionIds(); if(!action.length){ showFlash('error', 'Please select at least one user in the table'); return false; } syncActionHidden(); return confirm('Are you sure you want to reset tokens for the selected users?'); }); $(document).on('click', function(e){ var t=$(e.target); if(!t.closest('#guideTokenDropdownPanel').length && !t.closest('#guideTokenDropdownToggle').length){ $('#guideTokenDropdownPanel').removeClass('is-open'); } }); });
        })(jQuery);
    ");
?>

<h3 style="margin-left:18px;">Refresh Browser Tokens for Selected Users</h3>

<?php
if (Yii::app()->user->hasFlash('errorMsg')) {
    echo '<div class="flash-error guide-token-flash">' . Yii::app()->user->getFlash('errorMsg') . '</div>';
}
if (Yii::app()->user->hasFlash('successMsg')) {
    echo '<div class="flash-success guide-token-flash">' . Yii::app()->user->getFlash('successMsg') . '</div>';
}
?>
<div id="guideTokenFlashArea"></div>

<?php echo CHtml::beginForm(Yii::app()->createUrl('site/resetGuideTokensForSelectedUsers'), 'post', array('id' => 'resetTokenForm')); ?>
<div id="guideTokenHiddenSelectedInputs"></div>
<div id="guideTokenActionSelectedInputs"></div>

<div class="guide-token-reset-panel">
    <div class="guide-token-reset-panel__header">
        <div class="guide-token-reset-panel__title">Select users</div>
        <p class="guide-token-reset-panel__subtitle">Pick multiple users from the dropdown. Selections are saved automatically.</p>
    </div>

    <div class="guide-token-reset-toolbar">
        <div class="guide-token-dropdown-trigger-wrap">
            <button type="button" id="guideTokenDropdownToggle" class="guide-token-reset-action-button guide-token-reset-action-button--secondary guide-token-dropdown-trigger">
                <span>Select users</span>
                <span class="guide-token-dropdown-trigger__count" id="guideTokenDropdownCount"><?php echo count($savedSelectedUsers); ?> selected</span>
            </button>
        </div>
        <div class="guide-token-reset-meta"><?php echo count($savedSelectedUsers); ?> selected, <?php echo count($users); ?> total</div>
        <div class="guide-token-reset-actions-top">
            <?php echo CHtml::link('Download log file', array('site/downloadResetGuideTokensLog'), array('class' => 'guide-token-reset-action-button guide-token-download-button')); ?>
        </div>
    </div>

    <div id="guideTokenDropdownPanel" class="guide-token-dropdown-panel">
        <div class="guide-token-dropdown-panel__header">
            <div class="guide-token-reset-search">
                <input type="text" id="guideTokenUserSearch" placeholder="Search by name, login, email or ID">
            </div>
        </div>
        <div id="guideTokenDropdownList" class="guide-token-dropdown-list"></div>
    </div>

    <div class="guide-token-selected-section">
        <div class="guide-token-selected-section__header">
            <h4 class="guide-token-selected-section__title">Selected users table</h4>
            <div id="guideTokenSelectedSectionCount" class="guide-token-selected-section__count"><?php echo count($savedSelectedUsers); ?> selected</div>
        </div>
        <p class="guide-token-selected-section__note">The tokens for the users in the selected users table below will automatically refresh daily. You can also reset the tokens manually in bulk by selecting the specific/all users in the table and clicking the Reset Tokens button.</p>
        <div id="guideTokenSelectedTableWrap" class="guide-token-selected-table-wrap">
        <table class="guide-token-selected-table">
            <thead>
                <tr>
                    <th class="guide-token-selected-table__check"><input type="checkbox" id="guideTokenTableSelectAll"></th>
                    <th class="guide-token-selected-table__id">ID</th>
                    <th>Login</th>
                    <th>Email</th>
                    <th>First name</th>
                    <th>Last name</th>
                </tr>
            </thead>
            <tbody id="guideTokenSelectedTableBody"></tbody>
        </table>
        <div id="guideTokenSelectedTableEmpty" class="guide-token-selected-table-empty">No users selected yet.</div>
        </div>
    </div>
</div>

<div class="guide-token-footer-actions">
    <?php echo CHtml::button('Remove Selected Users', array('id' => 'guideTokenRemoveSelected', 'class' => 'guide-token-reset-action-button guide-token-remove-button')); ?>
    <?php echo CHtml::submitButton('Reset Tokens for Selected Users', array('name' => 'executeReset', 'value' => 'Reset Tokens', 'id' => 'guideTokenResetSubmit', 'class' => 'guide-token-reset-action-button guide-token-reset-action-button--primary')); ?>
</div>

<p class="guide-token-note">Selections are saved automatically as you choose or remove users.</p>

<?php echo CHtml::endForm(); ?>

<?php } else { $this->redirect('index.php'); } ?>
