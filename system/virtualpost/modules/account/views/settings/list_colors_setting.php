<style>
    .color_code{
        width: 160px;
    }
    .table_color_code th{
        vertical-align: middle;
    }
</style>
<?php
$listColor = APContext::getListColors($is_admin_site);
?>
<!-- color setting -->
<table class="table_color_code">
    <tr>
        <th style="width: 250px;">Main Color</th>
        <td><input  type="text" id="COLOR_001" name="COLOR_001" value="<?php echo $listColor['COLOR_001'] ?>" class="input-width color_code" style="background: #<?php echo $listColor['COLOR_001'] ?>" /></td>
    </tr>
    
    <tr>
        <th>Secondary Color</th>
        <td><input  type="text" id="COLOR_002" name="COLOR_002" value="<?php echo $listColor['COLOR_002'] ?>" class="input-width color_code" style="background: #<?php echo $listColor['COLOR_002'] ?>" /></td>
    </tr>

    <tr>
        <th>Tertiary Color</th>
        <td><input  type="text" id="COLOR_003" name="COLOR_003" value="<?php echo $listColor['COLOR_003'] ?>" class="input-width color_code" style="background: #<?php echo $listColor['COLOR_003'] ?>" /></td>
    </tr>
    
    <tr>
        <th>Text on Main Color</th>
        <td><input type="text" id="COLOR_004" name="COLOR_004" value="<?php echo $listColor['COLOR_004'] ?>" class="input-width color_code" style="background: #<?php echo $listColor['COLOR_004'] ?>" /></td>
    </tr>
    
    <tr>
        <th>Text on Secondary Color</th>
        <td><input  type="text" id="COLOR_005" name="COLOR_005" value="<?php  echo $listColor['COLOR_005'] ?>" class="input-width color_code" style="background: #<?php echo $listColor['COLOR_005'] ?>" /></td>
    </tr>
    
    <tr>
        <th>Text on Tertiary Color</th>
        <td><input  type="text" id="COLOR_006" name="COLOR_006" value="<?php echo $listColor['COLOR_006'] ?>" class="input-width color_code" style="background: #<?php echo $listColor['COLOR_006'] ?>" /></td>
    </tr>

    <tr>
        <th>Grey 1</th>
        <td><input  type="text" id="COLOR_007" name="COLOR_007" value="<?php echo $listColor['COLOR_007'] ?>" class="input-width color_code" style="background: #<?php echo $listColor['COLOR_007'] ?>" /></td>
    </tr>
    <tr>
        <th>Grey 2</th>
        <td><input type="text" id="COLOR_008" name="COLOR_008" value="<?php echo $listColor['COLOR_008'] ?>" class="input-width color_code" style="background: #<?php echo $listColor['COLOR_008'] ?>" /></td>
    </tr>
    
    <tr>
        <th>Grey 3</th>
        <td><input  type="text" id="COLOR_009" name="COLOR_009" value="<?php  echo $listColor['COLOR_009'] ?>" class="input-width color_code" style="background: #<?php echo $listColor['COLOR_009'] ?>" /></td>
    </tr>
    <tr>
        <th>Grey 4</th>
        <td><input  type="text" id="COLOR_010" name="COLOR_010" value="<?php echo $listColor['COLOR_010'] ?>" class="input-width color_code" style="background: #<?php echo $listColor['COLOR_010'] ?>" /></td>
    </tr>

    <tr>
        <th>Text on Grey 1</th>
        <td><input  type="text" id="COLOR_011" name="COLOR_011" value="<?php echo $listColor['COLOR_011'] ?>" class="input-width color_code" style="background: #<?php echo $listColor['COLOR_011'] ?>" /></td>
    </tr>
    
    <tr>
        <th>Text on Grey 2</th>
        <td><input type="text" id="COLOR_012" name="COLOR_012" value="<?php echo $listColor['COLOR_012'] ?>" class="input-width color_code" style="background: #<?php echo $listColor['COLOR_012'] ?>" /></td>
    </tr>
    
    <tr>
        <th>Text on Grey 3</th>
        <td><input  type="text" id="COLOR_013" name="COLOR_013" value="<?php echo $listColor['COLOR_013'] ?>" class="input-width color_code" style="background: #<?php echo $listColor['COLOR_013'] ?>" /></td>
    </tr>
    
    <tr>
        <th>Text on Grey 4</th>
        <td><input  type="text" id="COLOR_014" name="COLOR_014" value="<?php echo $listColor['COLOR_014'] ?>" class="input-width color_code" style="background: #<?php echo $listColor['COLOR_014'] ?>" /></td>
    </tr>

    <tr>
        <th>Composite Color 1</th>
        <td><input  type="text" id="COLOR_015" name="COLOR_015" value="<?php echo $listColor['COLOR_015'] ?>" class="input-width color_code" style="background: #<?php echo $listColor['COLOR_015'] ?>" /></td>
    </tr>
    
    <tr>
        <th>Composite Color 2</th>
        <td><input type="text" id="COLOR_016" name="COLOR_016" value="<?php echo $listColor['COLOR_016'] ?>" class="input-width color_code" style="background: #<?php echo $listColor['COLOR_016'] ?>" /></td>
    </tr>
    
    <tr>
        <th>Composite Color 3</th>
        <td><input  type="text" id="COLOR_017" name="COLOR_017" value="<?php echo $listColor['COLOR_017'] ?>" class="input-width color_code" style="background: #<?php echo $listColor['COLOR_017'] ?>" /></td>
    </tr>

    <tr>
        <th>Text on Composite Color 1</th>
        <td><input  type="text" id="COLOR_018" name="COLOR_018" value="<?php echo $listColor['COLOR_018'] ?>" class="input-width color_code" style="background: #<?php echo $listColor['COLOR_018'] ?>" /></td>
    </tr>
    
    <tr>
        <th>Text on Composite Color 2</th>
        <td><input type="text" id="COLOR_019" name="COLOR_019" value="<?php echo $listColor['COLOR_019'] ?>" class="input-width color_code" style="background: #<?php echo $listColor['COLOR_019'] ?>" /></td>
    </tr>
    
    <tr>
        <th>Text on Composite Color 3</th>
        <td><input  type="text" id="COLOR_020" name="COLOR_020" value="<?php echo $listColor['COLOR_020'] ?>" class="input-width color_code" style="background: #<?php echo $listColor['COLOR_020'] ?>" /></td>
    </tr>

    <tr>
        <th>Button on Main Color</th>
        <td><input  type="text" id="COLOR_021" name="COLOR_021" value="<?php echo $listColor['COLOR_021'] ?>" class="input-width color_code" style="background: #<?php echo $listColor['COLOR_021'] ?>" /></td>
    </tr>
    
    <tr>
        <th>Button on Main Color Onmouseover</th>
        <td><input type="text" id="COLOR_022" name="COLOR_022" value="<?php echo $listColor['COLOR_022'] ?>" class="input-width color_code" style="background: #<?php echo $listColor['COLOR_022'] ?>" /></td>
    </tr>
    
    <tr>
        <th>Button on Main Color Active</th>
        <td><input  type="text" id="COLOR_023" name="COLOR_023" value="<?php echo $listColor['COLOR_023'] ?>" class="input-width color_code" style="background: #<?php echo $listColor['COLOR_023'] ?>" /></td>
    </tr>
    
    <tr>
        <th>Button border on Main Color</th>
        <td><input  type="text" id="COLOR_024" name="COLOR_024" value="<?php echo $listColor['COLOR_024'] ?>" class="input-width color_code" style="background: #<?php echo $listColor['COLOR_024'] ?>" /></td>
    </tr>

    <tr>
        <th>Button on Secondary Color</th>
        <td><input  type="text" id="COLOR_025" name="COLOR_025" value="<?php echo $listColor['COLOR_025'] ?>" class="input-width color_code" style="background: #<?php echo $listColor['COLOR_025'] ?>" /></td>
    </tr>
    <tr>
        <th>Button on Secondary Color Onmouseover</th>
        <td><input type="text" id="COLOR_026" name="COLOR_026" value="<?php echo $listColor['COLOR_026'] ?>" class="input-width color_code" style="background: #<?php echo $listColor['COLOR_026'] ?>" /></td>
    </tr>
    <tr>
        <th>Button on Secondary Color Active</th>
        <td><input  type="text" id="COLOR_027" name="COLOR_027" value="<?php echo $listColor['COLOR_027'] ?>" class="input-width color_code" style="background: #<?php echo $listColor['COLOR_027'] ?>" /></td>
    </tr>
    <tr>
        <th>Button border on Secondary Color</th>
        <td><input  type="text" id="COLOR_028" name="COLOR_028" value="<?php echo $listColor['COLOR_028'] ?>" class="input-width color_code" style="background: #<?php echo $listColor['COLOR_028'] ?>" /></td>
    </tr>

    <tr>
        <th>Button on Grey 1</th>
        <td><input  type="text" id="COLOR_029" name="COLOR_029" value="<?php echo $listColor['COLOR_029'] ?>" class="input-width color_code" style="background: #<?php echo $listColor['COLOR_029'] ?>" /></td>
    </tr>
    
    <tr>
        <th>Button on Grey 1 Onmouseover</th>
        <td><input type="text" id="COLOR_030" name="COLOR_030" value="<?php echo $listColor['COLOR_030'] ?>" class="input-width color_code" style="background: #<?php echo $listColor['COLOR_030'] ?>" /></td>
    </tr>

    <tr>
        <th>Button on Grey 1 Active</th>
        <td><input  type="text" id="COLOR_031" name="COLOR_031" value="<?php echo $listColor['COLOR_031'] ?>" class="input-width color_code" style="background: #<?php echo $listColor['COLOR_031'] ?>" /></td>
    </tr>
    
    <tr>
        <th>Button border on Grey1</th>
        <td><input  type="text" id="COLOR_032" name="COLOR_032" value="<?php echo $listColor['COLOR_032'] ?>" class="input-width color_code" style="background: #<?php echo $listColor['COLOR_032'] ?>" /></td>
    </tr>

    <tr>
        <th>Button Text Color Main</th>
        <td><input  type="text" id="COLOR_033" name="COLOR_033" value="<?php echo  $listColor['COLOR_033'] ?>" class="input-width color_code" style="background: #<?php echo  $listColor['COLOR_033'] ?>" /></td>
    </tr>
    
    <tr>
        <th>Button Text Color Main Onmouseover</th>
        <td><input type="text" id="COLOR_034" name="COLOR_034" value="<?php echo  $listColor['COLOR_034'] ?>" class="input-width color_code" style="background: #<?php echo  $listColor['COLOR_034'] ?>" /></td>
    </tr>
    
    <tr>
        <th>Button Text Color Main Active</th>
        <td><input  type="text" id="COLOR_035" name="COLOR_035" value="<?php echo  $listColor['COLOR_035'] ?>" class="input-width color_code" style="background: #<?php echo  $listColor['COLOR_035'] ?>" /></td>
    </tr>
    

    <tr>
        <th>Button Text Color Secondary</th>
        <td><input  type="text" id="COLOR_036" name="COLOR_036" value="<?php echo  $listColor['COLOR_036'] ?>" class="input-width color_code" style="background: #<?php echo  $listColor['COLOR_036'] ?>" /></td>
    </tr>
    
    <tr>
        <th>Button Text Color Secondary Onmouseover</th>
        <td><input type="text" id="COLOR_037" name="COLOR_037" value="<?php echo  $listColor['COLOR_037'] ?>" class="input-width color_code" style="background: #<?php echo  $listColor['COLOR_037'] ?>" /></td>
    </tr>
    
    <tr>
        <th>Button Text Color Secondary Active</th>
        <td><input  type="text" id="COLOR_038" name="COLOR_038" value="<?php echo  $listColor['COLOR_038'] ?>" class="input-width color_code" style="background: #<?php echo  $listColor['COLOR_038'] ?>" /></td>
    </tr>

    <tr>
        <th>Button Text Color Tertiary</th>
        <td><input  type="text" id="COLOR_039" name="COLOR_039" value="<?php echo  $listColor['COLOR_039'] ?>" class="input-width color_code" style="background: #<?php echo  $listColor['COLOR_039'] ?>" /></td>
    </tr>
    
    <tr>
        <th>Button Text Color Tertiary Onmouseover</th>
        <td><input type="text" id="COLOR_040" name="COLOR_040" value="<?php echo  $listColor['COLOR_040'] ?>" class="input-width color_code" style="background: #<?php echo  $listColor['COLOR_040'] ?>" /></td>
    </tr>
    
    <tr>
        <th>Button Text Color Tertiary Active</th>
        <td><input  type="text" id="COLOR_041" name="COLOR_041" value="<?php echo  $listColor['COLOR_041'] ?>" class="input-width color_code" style="background: #<?php echo  $listColor['COLOR_041'] ?>" /></td>
    </tr>

    <tr>
        <th>Link on Main Color</th>
        <td><input  type="text" id="COLOR_042" name="COLOR_042" value="<?php echo  $listColor['COLOR_042'] ?>" class="input-width color_code" style="background: #<?php echo  $listColor['COLOR_042'] ?>" /></td>
    </tr>
    
    <tr>
        <th>Link on Main Color Onmouseover</th>
        <td><input type="text" id="COLOR_043" name="COLOR_043" value="<?php echo  $listColor['COLOR_043'] ?>" class="input-width color_code" style="background: #<?php echo  $listColor['COLOR_043'] ?>" /></td>
    </tr>
    
    <tr>
        <th>Link on Main Color Active</th>
        <td><input  type="text" id="COLOR_044" name="COLOR_044" value="<?php echo  $listColor['COLOR_044'] ?>" class="input-width color_code" style="background: #<?php echo  $listColor['COLOR_044'] ?>" /></td>
    </tr>
    
    <tr>
        <th>Link on Main Color Active background</th>
        <td><input  type="text" id="COLOR_064" name="COLOR_064" value="<?php echo  $listColor['COLOR_064'] ?>" class="input-width color_code" style="background: #<?php echo  $listColor['COLOR_064'] ?>" /></td>
    </tr>
    
    <tr>
        <th>Link on Main Color onmouseover background</th>
        <td><input  type="text" id="COLOR_065" name="COLOR_064" value="<?php echo  $listColor['COLOR_065'] ?>" class="input-width color_code" style="background: #<?php echo  $listColor['COLOR_065'] ?>" /></td>
    </tr>

    <tr>
        <th>Link on Secondary Color</th>
        <td><input  type="text" id="COLOR_045" name="COLOR_045" value="<?php echo  $listColor['COLOR_045'] ?>" class="input-width color_code" style="background: #<?php echo  $listColor['COLOR_045'] ?>" /></td>
    </tr>
    
    <tr>
        <th>Link on Secondary Color Onmouseover</th>
        <td><input type="text" id="COLOR_046" name="COLOR_046" value="<?php echo  $listColor['COLOR_046'] ?>" class="input-width color_code" style="background: #<?php echo  $listColor['COLOR_046'] ?>" /></td>
    </tr>
    
    <tr>
        <th>Link on Secondary Color Active</th>
        <td><input  type="text" id="COLOR_047" name="COLOR_047" value="<?php echo  $listColor['COLOR_047'] ?>" class="input-width color_code" style="background: #<?php echo  $listColor['COLOR_047'] ?>" /></td>
    </tr>

    <tr>
        <th>Link on Grey 1</th>
        <td><input  type="text" id="COLOR_048" name="COLOR_048" value="<?php echo  $listColor['COLOR_048'] ?>" class="input-width color_code" style="background: #<?php echo  $listColor['COLOR_048'] ?>" /></td>
    </tr>
    
    <tr>
        <th>Link on Grey 1 Onmouseover</th>
        <td><input type="text" id="COLOR_049" name="COLOR_049" value="<?php echo  $listColor['COLOR_049'] ?>" class="input-width color_code" style="background: #<?php echo  $listColor['COLOR_049'] ?>" /></td>
    </tr>
    
    <tr>
        <th>Link on Grey 1 Active</th>
        <td><input  type="text" id="COLOR_050" name="COLOR_050" value="<?php echo  $listColor['COLOR_050'] ?>" class="input-width color_code" style="background: #<?php echo  $listColor['COLOR_050'] ?>" /></td>
    </tr>

    <tr>
        <th>Search Box Color</th>
        <td><input  type="text" id="COLOR_051" name="COLOR_051" value="<?php echo  $listColor['COLOR_051'] ?>" class="input-width color_code" style="background: #<?php echo  $listColor['COLOR_051'] ?>" /></td>
    </tr>

    <tr>
        <th>Search Box Text&AMP;Icon Color</th>
        <td><input type="text" id="COLOR_052" name="COLOR_052" value="<?php echo  $listColor['COLOR_052'] ?>" class="input-width color_code" style="background: #<?php echo  $listColor['COLOR_052'] ?>" /></td>
    </tr>

    <tr>
        <th>Search Box imput color</th>
        <td><input  type="text" id="COLOR_053" name="COLOR_053" value="<?php echo  $listColor['COLOR_053'] ?>" class="input-width color_code" style="background: #<?php echo  $listColor['COLOR_053'] ?>" /></td>
    </tr>

    <tr>
        <th>Table Header Color</th>
        <td><input  type="text" id="COLOR_054" name="COLOR_054" value="<?php echo  $listColor['COLOR_054'] ?>" class="input-width color_code" style="background: #<?php echo  $listColor['COLOR_054'] ?>" /></td>
    </tr>
    
    <tr>
        <th>Table Header Text Color</th>
        <td><input type="text" id="COLOR_055" name="COLOR_055" value="<?php echo  $listColor['COLOR_055'] ?>" class="input-width color_code" style="background: #<?php echo  $listColor['COLOR_055'] ?>" /></td>
    </tr>
    
    <tr>
        <th>Table Onmouseover Color</th>
        <td><input  type="text" id="COLOR_056" name="COLOR_056" value="<?php echo  $listColor['COLOR_056'] ?>" class="input-width color_code" style="background: #<?php echo  $listColor['COLOR_056'] ?>" /></td>
    </tr>
    
    <tr>
        <th>Table Onmouseover Text Color</th>
        <td><input  type="text" id="COLOR_057" name="COLOR_057" value="<?php echo  $listColor['COLOR_057'] ?>" class="input-width color_code" style="background: #<?php echo  $listColor['COLOR_057'] ?>" /></td>
    </tr>

    <tr>
        <th>Table Active Color</th>
        <td><input  type="text" id="COLOR_058" name="COLOR_058" value="<?php echo  $listColor['COLOR_058'] ?>" class="input-width color_code" style="background: #<?php echo  $listColor['COLOR_058'] ?>" /></td>
    </tr>
    
    <tr>
        <th>Table Active Text Color</th>
        <td><input type="text" id="COLOR_059" name="COLOR_059" value="<?php echo  $listColor['COLOR_059'] ?>" class="input-width color_code" style="background: #<?php echo  $listColor['COLOR_059'] ?>" /></td>
    </tr>
    
    <tr>
        <th>Icon Color</th>
        <td><input  type="text" id="COLOR_060" name="COLOR_060" value="<?php echo  $listColor['COLOR_060'] ?>" class="input-width color_code" style="background: #<?php echo  $listColor['COLOR_060'] ?>" /></td>
    </tr>
    
    <tr>
        <th>Support Button color</th>
        <td><input  type="text" id="COLOR_061" name="COLOR_061" value="<?php echo  $listColor['COLOR_061'] ?>" class="input-width color_code" style="background: #<?php echo  $listColor['COLOR_061'] ?>" /></td>
    </tr>
    
    <tr>
        <th>Support Button text color</th>
        <td><input  type="text" id="COLOR_062" name="COLOR_062" value="<?php echo  $listColor['COLOR_062'] ?>" class="input-width color_code" style="background: #<?php echo  $listColor['COLOR_062'] ?>" /></td>
    </tr>

    <tr>
        <th>Header Text on Grey1</th>
        <td><input  type="text" id="COLOR_063" name="COLOR_063" value="<?php echo  $listColor['COLOR_063'] ?>" class="input-width color_code" style="background: #<?php echo  $listColor['COLOR_063'] ?>" /></td>
    </tr>
</table>
