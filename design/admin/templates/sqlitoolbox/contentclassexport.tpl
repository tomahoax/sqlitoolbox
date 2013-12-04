<div class="context-block">
<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

<h1 class="context-title">Export content classes definition</h1>

<div class="header-mainline"></div>

</div></div></div></div></div></div>
    <form id="classlist" action={$page_uri|ezurl()} method="post">

    <div class="box-ml"><div class="box-mr">
                <table class="list" cellspacing="0">
                    <tr>
                        <th class="export"><img src={'toggle-button-16x16.gif'|ezimage} alt="{'Invert selection.'|i18n( 'design/admin/node/view/full' )}" title="{'Invert selection.'|i18n( 'design/admin/node/view/full' )}" onclick="ezjs_toggleCheckboxes( document.classlist, 'ExportIDArray[]' ); return false;" /></th>
                        <th>{'Content Class ID'|i18n( 'extension/sqlitoolbox')}</th>
                        <th>{'Content Class Name'|i18n( 'extension/sqlitoolbox')}</th>
                        <th>{'Content Class Identifier'|i18n( 'extension/sqlitoolbox')}</th>
                        <th>{'Amount of objects'|i18n( 'extension/sqlitoolbox')}</th>
                        <th>{'Created'|i18n( 'extension/sqlitoolbox')}</th>
                        <th>{'Modified'|i18n( 'extension/sqlitoolbox')}</th>
                    </tr>

                    {foreach $classList as $k => $class sequence array( 'bglight', 'bgdark') as $style}
                        <tr class="{$style}">
                            <td>
                                <input name="ExportIDArray[]" type="checkbox" value="{$class.id}">
                            </td>
                            <td>
                                {$class.id}
                            </td>
                            <td>
                                {$class.name|wash()}
                            </td>
                            <td>
                                {$class.identifier|wash()}
                            </td>
                            <td>
                                {$objectCounts[$class.id]} {'content objects'|i18n( 'extension/sqlitoolbox')}
                            </td>
                            <td>
                                {$class.created|datetime('custom', '%M %d %Y %H:%i:%s')}
                            </td>
                            <td>
                                {$class.modified|datetime('custom', '%M %d %Y %H:%i:%s')}
                            </td>
                        </tr>
                    {/foreach}
                </table>


                </div></div></div>

    <div class="controlbar">
        <div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">
                                <div class="block">
                                    <div class="left">
                                        <input class="button" type="submit" name="ExportButton" value="{'Export selected'|i18n( 'design/admin/node/view/full' )}" title="{'Export the selected items from the list above.'|i18n( 'design/admin/node/view/full' )}" />
                                    </div>
                                    <div class="right">

                                    </div>
                                    <div class="break"></div>
                                </div>
                            </div></div></div></div></div></div>
    </div>
    </form>
</div>
