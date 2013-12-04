<div class="context-block">
<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

<h1 class="context-title">List generated mails</h1>

<div class="header-mainline"></div>

</div></div></div></div></div></div>

    <div class="box-ml"><div class="box-mr"><div class="box-content">
                <table class="list" cellspacing="0">
                    <tr>
                        <th>{'Generation time'|i18n( 'extension/sqlitoolbox')}</th>
                        <th>{'Subject'|i18n( 'extension/sqlitoolbox')}</th>
                        <th>{'From'|i18n( 'extension/sqlitoolbox')}</th>
                        <th>{'To (only first receiver is displayed here)'|i18n( 'extension/sqlitoolbox')}</th>
                        <th>{'Path'|i18n( 'extension/sqlitoolbox')}</th>
                        <th>{'Size'|i18n( 'extension/sqlitoolbox')}</th>
                    </tr>

                    {foreach $mailList as $mailFile => $details sequence array( 'bglight', 'bgdark') as $style}
                        <tr class="{$style}">
                            <td>
                                {$details['generation_time']|datetime('custom', '%M %d %Y %H:%i:%s')}
                            </td>
                            <td>

                                <a href={concat('sqlitoolbox/mailview/', $details.link)|ezurl()}>{$details['mail_subject']|wash}</a>
                            </td>
                            <td>
                                {$details['mail_from']|wash}
                            </td>
                            <td>
                                {$details['mail_to']||wash}
                            </td>
                            <td>
                                <a href={concat('sqlitoolbox/mailview/', $details.link)|ezurl()} title="{'Link to raw view'|i18n( 'extension/sqlitoolbox')}">{$details['path']}</a>
                            </td>
                            <td>
                                {$details['size']|si( byte, auto )}
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
