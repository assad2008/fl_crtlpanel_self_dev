<br />
<table id="page-table" cellspacing="0">
  <tr>
    <td align="right" nowrap="true">
		<{if $pages}>
        <div class="pg">
			<a>总数:<{$pages.numtotal}></a>
			<{if $pages.prev gt -1}>                            
            <a href="<{$page_url}>&start=<{$pages.prev}>" class="prev">上一页</a>
            <{/if}>
            <{foreach from=$pages key=k item=i}>
                <{if $k ne 'prev' && $k ne 'next'}>
                    <{if $k eq 'omitf' || $k eq 'omita'}>
                        <a>…</a>
                    <{elseif $k ne 'numtotal'}>
						<{if $i gt -1}>
							<a href="<{$page_url}>&start=<{$i}>"><{$k}></a>
						<{else}>
							<strong><{$k}></strong>
						<{/if}>
					<{/if}>   
				<{/if}>                             
            <{/foreach}>
            <{if $pages.next gt -1}>                            
                <a href="<{$page_url}>&start=<{$pages.next}>"  class="nxt" >下一页</a>
            <{/if}>
        </div>                
        <{/if}>
    </td>
  </tr>
</table>
<br />