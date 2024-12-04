<div class="row">
    <div class="form-group col-sm-6">
        <input type="text" value="<?=$customer->getShippingName()?>" class="form-control" name="fullname"
            placeholder="Họ và tên">
    </div>
    <div class="form-group col-sm-6">
        <input type="tel" value="<?=$customer->getShippingMobile()?>" class="form-control" name="mobile"
            placeholder="Số điện thoại">
    </div>
    <div class="form-group col-sm-4">
        <select name="province" class="form-control province">
            <option value="">Tỉnh / thành phố</option>
            <?php foreach ($provinces as $province): ?>
            <option <?=$selected_province_id == $province->getId() ? 'selected' : ''?> value="<?=$province->getId()?>">
                <?=$province->getName()?></option>
            <?php endforeach?>
        </select>
    </div>
    <div class="form-group col-sm-4">
        <select name="district" class="form-control district">
            <option value="">Quận / huyện</option>
            <?php foreach ($districts as $district): ?>
            <option <?=$selected_district_id == $district->getId() ? 'selected' : ''?> value="<?=$district->getId()?>">
                <?=$district->getName()?></option>
            <?php endforeach?>
        </select>
    </div>
    <div class="form-group col-sm-4">
        <select name="ward" class="form-control ward">
            <option value="">Phường / xã</option>
            <?php foreach ($wards as $ward): ?>
            <option <?=$selected_ward_id == $ward->getId() ? 'selected' : ''?> value="<?=$ward->getId()?>">
                <?=$ward->getName()?></option>
            <?php endforeach?>
        </select>
    </div>
    <div class="form-group col-sm-12">
        <input type="text" value="<?=$customer->getHousenumberStreet()?>" class="form-control" placeholder="Địa chỉ"
            name="address">
    </div>
</div>