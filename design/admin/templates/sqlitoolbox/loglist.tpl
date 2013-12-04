<div class="context-block">
<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

<h1 class="context-title">List logs on server</h1>

<div class="header-mainline"></div>

</div></div></div></div></div></div>

    <div class="box-ml"><div class="box-mr"><div class="box-content">
                <table class="list" cellspacing="0">
                    <tr>
                        <th>{'Name'|i18n( 'extension/sqlitoolbox')}</th>
                        <th>{'Path'|i18n( 'extension/sqlitoolbox')}</th>
                        <th>{'Files'|i18n( 'extension/sqlitoolbox')}</th>
                        <th>{'Total size'|i18n( 'extension/sqlitoolbox')}</th>
                        <th>{'Last updated'|i18n( 'extension/sqlitoolbox')}</th>
                    </tr>

                    {foreach $logList as $cache => $details sequence array( 'bglight', 'bgdark') as $style}
                        <tr class="{$style}">
                            <td>
                                {$cache|wash}
                            </td>
                            <td>
                                <a href={concat('sqlitoolbox/logview/', $details.link)|ezurl()} title="{'Link to raw view'|i18n( 'extension/sqlitoolbox')}">{$details['path']}</a>
                            </td>
                            <td>
                                {$details['count']}
                            </td>
                            <td>
                                {$details['size']|si( byte, auto )}
                            </td>
                            <td>
                                {$details['modified']|datetime('custom', '%M %d %Y %H:%i:%s')}
                            </td>
                        </tr>
                    {/foreach}
                </table>

                </div></div></div>

    <div class="controlbar">
    <div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-tc"><div class="box-bl"><div class="box-br">
    </div></div></div></div></div></div>
    </div>
</div>
