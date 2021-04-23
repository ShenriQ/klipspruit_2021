<div class="invoice-preview-container bg-white" style="font-size: 85%;">
    <div style=" margin: auto;">
        <table style="color: #444; width: 100%;">
            <tbody>
                <tr class="invoice-preview-header-row">
                    <td style="width: 45%; text-align: left; vertical-align: top;">
                        <div style="font-size: 35px;font-weight: bold; line-height: 15px;"><?php echo lang('c_158'); ?></div>
                        <div style="line-height: 10px;"></div><span style="font-size: 16px;font-weight:bold;"><?php echo $estimate->getSubject(); ?></span>
                        <?php $estimate_project = $estimate->getProject();
                        echo (isset($estimate_project) ? '<br><span>' . lang('c_23') . ': ' . $estimate_project->getName() . '</span>' : ''); ?>
                    </td>
                    <td style="width: 20%;"></td>
                    <td style="width: 35%; vertical-align: top; text-align: right"><span class="invoice-info-title" style="font-size:20px; font-weight: bold;background-color: <?php echo config_option('invoice_color', '#00A65A');?>; color: #fff;">&nbsp;<?php echo $estimate->getEstimateNo(); ?>&nbsp;</span>
                        <div style="line-height: 10px;"></div><span><?php echo lang('c_162'); ?>: <?php echo date("F j, Y", $estimate->getCreatedAt()) ?></span>
                        <br> <span><?php echo lang('c_138'); ?>: <?php echo date("F j, Y", $estimate->getDueDate()) ?></span>
                        <?php $estimate_duedate = $estimate->getDueDate();
                        if(isset($estimate_duedate) && $estimate_duedate < time() && $estimate->getStatus()) { echo "<br><span style=\"font-weight:bold;color:red;\">".lang('c_154')."</span>"; } ?>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 5px;"></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>
                        <div style="font-size:18px; font-weight: bold;"><?php echo lang('c_159.1'); ?></div>
                        <div style="line-height: 2px; border-bottom: 1px solid #f3f3f3;"></div>
                        <div style="line-height: 3px;"></div> <?php $created_by = $estimate->getCreatedBy();
                        if($c_company = $created_by->getCompany()) : ?><strong><?php echo $c_company->getName(); ?></strong> <?php echo ($c_company->getVatNo() != "" ? "<u>(".lang('c_523.118').") " . $c_company->getVatNo() . "</u>" : ""); ?><?php endif; ?>
                        <div style="line-height: 3px;"></div>
                        <span class="invoice-meta" style="font-size: 90%; color: #666;"><?php if($c_company->getAddress() != "") : ?><?php echo $c_company->getAddress(); ?><br><?php endif; ?><?php if($c_company->getPhoneNumber() != "") : ?><?php echo $c_company->getPhoneNumber();?><br><?php endif; ?>
                        </span>
                    </td>
                    <td></td>
                    <td>
                        <div style="font-size:18px; font-weight: bold;"><?php echo lang('c_160'); ?></div>
                        <div style="line-height: 2px; border-bottom: 1px solid #f3f3f3;"></div>
                        <div style="line-height: 3px;"></div><?php $client_to = $estimate->getClient();
                        if($estimate->getCompanyName() != "") : ?><strong><?php echo $estimate->getCompanyName(); ?></strong><?php endif; ?>
                        <div style="line-height: 3px;"></div>
                        <span class="invoice-meta" style="font-size: 90%; color: #666;"><?php echo $client_to->getName(); ?><br><small style="color: blue; font-size: 12px;"><?php echo $client_to->getEmail(); ?> / <u>#<?php echo "CUS-".str_pad($client_to->getId(), 6, '0', STR_PAD_LEFT); ?></u></small>
                        <?php if($estimate->getCompanyAddress() != "") : ?><br><?php echo $estimate->getCompanyAddress(); endif; ?>
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <br>
    <table class="table-responsive" style="width: 100%; color: #444;">
        <tbody>
            <tr style="font-weight: bold; background-color: <?php echo config_option('invoice_color', '#00A65A');?>; color: #fff;">
                <th style="width: 45%; border-right: 1px solid #eee;"><?php echo lang('c_163'); ?></th>
                <th style="text-align: center;  width: 15%; border-right: 1px solid #eee;"><?php echo lang('c_142'); ?></th>
                <th style="text-align: right;  width: 20%; border-right: 1px solid #eee;"><?php echo lang('c_143'); ?></th>
                <th style="text-align: right;  width: 20%; "><?php echo lang('c_144'); ?></th>
            </tr>
            
            <?php $sub_total = 0;
            $items = $estimate->getItems();
            if(isset($items) && is_array($items) && count($items)) :
            foreach($items as $r) : ?>
            <?php $total = number_format($r->getQuantity()*$r->getAmount(),2);
            $sub_total += $r->getAmount()*$r->getQuantity(); ?>
            
            <tr style="background-color: #f4f4f4;"><td style="width: 45%; border: 1px solid #fff; padding: 10px;"><?php echo $r->getDescription(); ?></td><td style="text-align: center; width: 15%; border: 1px solid #fff;"><?php echo $r->getQuantity(); ?></td><td style="text-align: right; width: 20%; border: 1px solid #fff;"><?php echo $r->getAmount() ?></td><td style="text-align: right; width: 20%; border: 1px solid #fff;"><?php echo $total ?></td></tr>
            <?php endforeach; 
            else : ?><tr><td colspan="4"><?php echo lang('e_2'); ?></td></tr><?php endif; ?>
            <?php $total_amount = $sub_total; ?>
            <tr>
                <td colspan="3" style="text-align: right;"><?php echo lang('c_144'); ?></td>
                <td style="text-align: right; width: 20%; border: 1px solid #fff; background-color: #f4f4f4;"><?php echo number_format($sub_total,2) ?></td>
            </tr>

            <?php if($estimate->getTaxRate() > 0) : ?>
            <?php $tax_addon = abs($sub_total/100*$estimate->getTaxRate());
            $total_amount += $tax_addon; ?>
            <tr>
                <td colspan="3" style="text-align: right;"><?php echo lang('c_146'); ?>
                <?php if($estimate->getTax() != "") : ?> (<?php echo $estimate->getTax() ?>) <?php endif; ?>@ <?php echo $estimate->getTaxRate() ?>%</td>
                <td style="text-align: right; width: 20%; border: 1px solid #fff; background-color: #f4f4f4;"><?php echo number_format($tax_addon,2) ?></td>
            </tr>
            <?php endif; ?>
            
            <?php if($estimate->getTaxRate2() > 0) : ?>
            <?php $tax_addon2 = abs($sub_total/100*$estimate->getTaxRate2());
            $total_amount += $tax_addon2; ?>
            <tr>
                <td colspan="3" style="text-align: right;"><?php echo lang('c_146.1'); ?>
                <?php if($estimate->getTax2() != "") : ?> (<?php echo $estimate->getTax2() ?>) <?php endif; ?>@ <?php echo $estimate->getTaxRate2() ?>%</td>
                <td style="text-align: right; width: 20%; border: 1px solid #fff; background-color: #f4f4f4;"><?php echo number_format($tax_addon2,2) ?></td>
            </tr>
            <?php endif; ?>
            
            <tr>
                <td colspan="3" style="text-align: right;"><?php echo lang('c_149'); ?></td>
                <td style="text-align: right; width: 20%; border: 1px solid #fff; background-color: #efefef;"><?php echo config_option('default_currency', "$"); ?><?php echo number_format($total_amount,2) ?></td>
            </tr>

            <?php 
                $calculated_discount_amount = (int) $estimate->getDiscountAmount();
                if($calculated_discount_amount > 0) {
                    if($estimate->getDiscountAmountType() == 'percentage') {
                        $calculated_discount_amount = abs($calculated_discount_amount/100*$total_amount);
                    }
                }
                $total_amount = ($total_amount - $calculated_discount_amount);
            ?>
            <?php if($calculated_discount_amount > 0) : ?>
            <tr>
                <td colspan="3" style="text-align: right;"><?php echo lang('c_523.58'); ?> (<?php echo $estimate->getDiscountAmountType() == 'percentage' ? $estimate->getDiscountAmount() . lang('c_523.59') : lang('c_523.60'); ?>)</td>
                <td style="text-align: right; width: 20%; border: 1px solid #fff; background-color: #ffffce;">-<?php echo config_option('default_currency', "$"); ?><?php echo number_format($calculated_discount_amount,2) ?></td>
            </tr>
            <?php endif; ?>
            <tr>
                <td colspan="3" style="text-align: right;"><?php echo lang('c_149.1'); ?></td>
                <td style="text-align: right; width: 20%; background-color: #45B6FE; color: #fff;"><?php echo config_option('default_currency', "$"); ?><?php echo number_format($total_amount,2) ?></td>
            </tr>
        </tbody>
    </table>
    <br>
    <br>
    <?php if($estimate->getNote() != "") : ?>
        <div style="border-top: 2px solid #f3f3f3; color:#444; padding:0 0 20px 0;">
        <br><?php echo $estimate->getNote();?></div>
    <?php endif; ?>
    <span style="color:#444; line-height: 14px;"></span>

</div>