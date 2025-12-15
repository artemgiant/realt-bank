<div class="modal fade" id="geoModal" tabindex="-1" aria-labelledby="geoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <div class="modal-body-l d-flex align-items-center justify-content-between">
                    <h2 class="modal-title" id="geoModalLabel">
                        <span>Локация</span>
                    </h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body-l">
                    <label for="address">Адрес</label>
                    <input type="text" id="address" class="form-control" name="geo_address" placeholder="Введите адрес">
                </div>
                <div class="modal-body-l">
                    <div class="row">
                        <div class="col-12">
                            <span class="label">Координаты</span>
                        </div>
                        <div class="col-6">
                            <label class="d-block">
                                <input type="text" id="latitude" class="form-control" name="latitude" placeholder="Широта" readonly>
                            </label>
                        </div>
                        <div class="col-6">
                            <label class="d-block">
                                <input type="text" id="longitude" class="form-control" name="longitude" placeholder="Долгота" readonly>
                            </label>
                        </div>
                    </div>
                </div>
                <div id="map-container" style="height: 400px; width: 100%; margin-top: 15px;"></div>
                <div class="modal-body-l mt-3">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal" id="save-geo-btn">Сохранить</button>
                </div>
            </div>
        </div>
    </div>
</div>
