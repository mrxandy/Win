/**
 * ���������ʾ�� Google Map API �����¹���:
 *   * ����ק�ı�ע
 *   * �ڵ�ͼ�ϸ�������
 *   * ����������
 *   * �¼�������������ק��
 *   * ��Ϣ��ʾ���ڣ����ݴ��ڣ�
 *   * ��������ά�����ֶ���
 *   * �Զ���ؼ�
 *
 * ע�⣺Ϊ���� IE6 ��������ʾ���ߣ���������ҳ�� <HTML> ��ǩ�м��ϣ�
 *   <html xmlns:v="urn:schemas-microsoft-com:vml">
 *
 * @author haogang
 */

/**
 * ��ʾ����һ��˫������ά���û��趨�ı�ע���ܹ����׵�ʵ�ֱ�ע�ı�����ɾ��
 * ÿ�������� m �������ֶΣ�
 *    m.prev      ǰ����ע
 *    m.next      ��̱�ע
 *    m.segPrev   ���ӱ���ע��ǰ����ע���߶�
 *    m.segNext   ���ӱ���ע���̱�ע���߶�
 */
function GRulerControl() {
  var me = this;
  
  // �ɹ��ʻ����ַ���
  me.RESET_BUTTON_TITLE_ = '������в���ע';
  me.ENABLE_BUTTON_TITLE_ = '��Ӳ���ע�����ã������������';
  me.DISABLE_BUTTON_TITLE_ = '��Ӳ���ע�ѽ��ã�������������';
  me.DELETE_BUTTON_TITLE_ = 'ɾ��';
  
  me.RESET_BUTTON_IMAGE_ = 'http://www.google.com/apis/maps/demo/distcal/images/ruler_clear.png';
  me.ENABLE_BUTTON_IMAGE_ = 'http://www.google.com/apis/maps/demo/distcal/images/ruler_enabled.png';
  me.DISABLE_BUTTON_IMAGE_ = 'http://www.google.com/apis/maps/demo/distcal/images/ruler_disabled.png';
  me.BACKGROUND_IMAGE_ = 'http://www.google.com/apis/maps/demo/distcal/images/ruler_background.png'
  
  me.KILOMETER_ = '����';
  me.METER_ = '��';
}

GRulerControl.prototype = new GControl();

/**
 * ��ʼ����߿ؼ�
 */
GRulerControl.prototype.initialize = function(map) {
  var me = this;
  var container = document.createElement('div');
  me.setButtonStyle_(container);
 
  // ������/���á���ť
  var btnEnable = document.createElement('img');
  btnEnable.width = btnEnable.height = 19;
  GEvent.addDomListener(btnEnable, 'click', 
    function() {
      me.setEnabled(!me.isEnabled());
    }
  );
  container.appendChild(btnEnable);
  
  // �����á���ť
  var btnReset = document.createElement('img');
  btnReset.width = btnReset.height = 19;
  btnReset.src = me.RESET_BUTTON_IMAGE_;
  btnReset.title = me.RESET_BUTTON_TITLE_;
  GEvent.addDomListener(btnReset, 'click', 
    function() {
      me.reset();
    }
  );
  container.appendChild(btnReset);
  
  // �����ǩ
  var txtInfo = document.createElement('div');
  txtInfo.style.font = 'small Arial';
  txtInfo.style.fontWeight = 'bold';
  txtInfo.style.fontSize = '9pt';
  txtInfo.style.width = "82px";
  container.appendChild(txtInfo);
  
  // ��ʼ���ڲ�����
  map.rulerControl_ = me;
  me.map_ = map;
  me.head_ = new Object();
  me.tail_ = new Object();
  me.head_.next_ = me.tail_;
  me.tail_.prev_ = me.head_;
  me.btnEnable_ = btnEnable;
  me.btnReset_ = btnReset;
  me.txtInfo_ = txtInfo;
  me.setEnabled(true);
  
  map.getContainer().appendChild(container);
  return container;
}


/**
 * ���ÿؼ��ĸ�ʽ
 */
GRulerControl.prototype.setButtonStyle_ = function(button) {
  button.style.backgroundImage = 'url(' + this.BACKGROUND_IMAGE_ + ')';
  button.style.font = "small Arial";
  button.style.border = "1px solid #888888";
  button.style.padding = "4px";
  button.style.textAlign = "right";
  button.style.cursor = "pointer";
}

/**
 * ��ǡ���ĸ�ʽ��ʾ����
 */
GRulerControl.prototype.formatDistance_ = function(len) {
  var me = this;
  
  len = Math.round(len);
  if (len <= 1000) {
    return len + ' ' + me.METER_;
  } else if (len <= 1000000) {
    return len / 1000 + ' ' + me.KILOMETER_;
  }
  return Math.round(len / 1000) + ' ' + me.KILOMETER_;
}

/**
 * ��ʽ���Ƕ�Ϊ�ַ���
 */
GRulerControl.prototype.formatDegree_ = function(value) {
  value = Math.abs(value);
  var v1 = Math.floor(value);
  var v2 = Math.floor((value - v1) * 60);
  var v3 = Math.round((value - v1) * 3600 % 60);
  return v1 + '��' + v2 + '\'' + v3 + '"';
}

/**
 * ��ʽ����γ��Ϊ�ַ���
 */
GRulerControl.prototype.formatLatLng_ = function(pt) {
  var me = this;
  
  var latName, lngName;
  var lat = pt.lat();
  var lng = pt.lng();
  latName = lat >= 0 ? '��γ' : '��γ';
  lngName = lng >= 0 ? '����' : '����';

  return lngName + me.formatDegree_(lng) + '��' 
    + latName + me.formatDegree_(lat);
}

/**
 * ���ؿؼ���Ĭ��λ��
 */
GRulerControl.prototype.getDefaultPosition = function() {
  return new GControlPosition(G_ANCHOR_TOP_RIGHT, new GSize(8, 8));
}

/**
 * ���ؿؼ��Ƿ�������
 */
GRulerControl.prototype.isEnabled = function() {
  return this.enabled_;
}

/**
 * ���ÿؼ��ġ�����/����"״̬
 */
GRulerControl.prototype.setEnabled = function(value) {
  var me = this;
  if (value == me.enabled_)
    return;
  me.enabled_ = value;
  
  if (me.enabled_) {
    me.mapClickHandle_ = GEvent.addListener(me.map_, 'click', me.onMapClick_);
    me.txtInfo_.style.display = 'block';
    me.btnReset_.style.display = 'inline';
    me.btnEnable_.src = me.ENABLE_BUTTON_IMAGE_;
    me.btnEnable_.title = me.ENABLE_BUTTON_TITLE_;
    me.updateDistance_();
  } else {
    GEvent.removeListener(me.mapClickHandle_);
    me.txtInfo_.style.display = 'none';
    me.btnReset_.style.display = 'none';
    me.btnEnable_.src = me.DISABLE_BUTTON_IMAGE_;
    me.btnEnable_.title = me.DISABLE_BUTTON_TITLE_;
  }
}

/**
 * �¼������������û�������ͼʱ��Ҫ�ڸ�λ�����һ����ע
 */
GRulerControl.prototype.onMapClick_ = function(marker, point) {
  var me = this.rulerControl_;
  
  // ����û��������Ǳ�ע���������ﴦ��
  if (marker)
    return;

  // ������ע������ӵ�������
  var newMarker = new GMarker(point, {draggable: true});

  var pos = me.tail_.prev_;
  newMarker.prev_ = pos;
  newMarker.next_ = pos.next_;
  pos.next_.prev_ = newMarker;
  pos.next_ = newMarker;

  // Ϊ��ע����¼�����������ק��עʱҪ���������߶κ;���
  GEvent.addListener(newMarker, 'dragend',
    function() {
      me.map_.closeInfoWindow();
      me.updateSegments_(newMarker);
      me.updateDistance_();
    }
  );
  // Ϊ��ע����¼���������������עʱҪ��ʾ���ݴ���
  GEvent.addListener(newMarker, 'click',
    function() {
      newMarker.openInfoWindow(me.createInfoWindow_(newMarker));
    }
  );
  
  // �������ı�ע��ӵ���ͼ��
  me.map_.addOverlay(newMarker);

  if (newMarker.prev_ != me.head_) {
    // ����ⲻ�ǵ�һ����ע���򴴽����ӵ���һ����ע���߶Σ�����ʾ�ڵ�ͼ��
    var segment = [newMarker.prev_.getPoint(), point];
    newMarker.segPrev_ = new GPolyline(segment);
    newMarker.prev_.segNext_ = newMarker.segPrev_;
    me.map_.addOverlay(newMarker.segPrev_);

    // ���¾�����ʾ
    me.updateDistance_();
  }
}

/**
 * ͳ���ܾ��룬����ʾ����ҳ��
 */
GRulerControl.prototype.updateDistance_ = function() {
  var me = this;
  var len = me.getDistance();
  
  // �����ʾ����ҳ��
  me.txtInfo_.innerHTML = me.formatDistance_(len);
}

/**
 * ��������ͳ���ܾ���
 */
GRulerControl.prototype.getDistance = function() {
  var me = this;
  var len = 0;
  
  // ���������ۼ�����������ע��ľ���
  for (var m = me.head_; m != me.tail_; m = m.next_) {
    if (m.prev_ && m.prev_.getPoint)
      len += m.prev_.getPoint().distanceFrom(m.getPoint());
  }
  return len;
}

/**
 * ������б�ע����ʼ������
 */
GRulerControl.prototype.reset = function() {
  var me = this;
  
  for (var m = me.head_.next_; m != me.tail_; m = m.next_) {
    me.map_.removeOverlay(m);
    if (m.segNext_)
      me.map_.removeOverlay(m.segNext_);
  }
  me.head_ = new Object();
  me.tail_ = new Object();
  me.head_.next_ = me.tail_;
  me.tail_.prev_ = me.head_;
  
  me.updateDistance_();
}


/**
 * �¼������������û���ק��ע����ע����ı�ʱ�����ã�����Ҫ������ñ�ע���ӵ��߶�
 * @param {GMarker} marker ����ק�ı�ע
 */
GRulerControl.prototype.updateSegments_ = function(marker) {
  var me = this;
  var segment;
  
  // ��������ǰ�����߶�
  if (marker.segPrev_ && marker.prev_.getPoint) {
    // �ӵ�ͼ��ɾ���ɵ��߶�
    me.map_.removeOverlay(marker.segPrev_);
    
    // ���ݱ�ע�ĵ�ǰ���깹���µ��߶Σ������������������ֶ�
    segment = [marker.prev_.getPoint(), marker.getPoint()];
    marker.segPrev_ = new GPolyline(segment);
    marker.prev_.segNext_ = marker.segPrev_;
    
    // �����߶���ӵ���ͼ��
    me.map_.addOverlay(marker.segPrev_);
  }
  
  // �������Ӻ�̵��߶Σ���������
  if (marker.segNext_ && marker.next_.getPoint) {
    me.map_.removeOverlay(marker.segNext_);
    segment = [marker.getPoint(), marker.next_.getPoint()];
    marker.segNext_ = new GPolyline(segment);
    marker.next_.segPrev_ = marker.segNext_;
    me.map_.addOverlay(marker.segNext_);
  }
}


/**
 * Ϊ������ʾ���ڴ��� DOM ���󣬰�����ע������͡�ɾ������ť
 * @param {GMarker} marker ��Ӧ�ı�ע
 */
GRulerControl.prototype.createInfoWindow_ = function(marker) {
  var me = this;
  
  // Ϊ������ʾ���ڴ�����̬ DOM �������������� DIV ��ǩ
  var div = document.createElement('div');
  div.style.fontSize = '10.5pt';
  div.style.width = '250px';
  div.appendChild(
    document.createTextNode(me.formatLatLng_(marker.getPoint())));
    
  var hr = document.createElement('hr');
  hr.style.border = 'solid 1px #cccccc';
  div.appendChild(hr);
  
  // ������ɾ������ť
  var lnk = document.createElement('div');
  lnk.innerHTML = me.DELETE_BUTTON_TITLE_;
  lnk.style.color = '#0000cc';
  lnk.style.cursor = 'pointer';
  lnk.style.textDecoration = 'underline';
  
  // Ϊ��ɾ������ť����¼��������� removePoint() �����¼������
  lnk.onclick =
    function() {
      me.map_.closeInfoWindow();
      me.removePoint_(marker);
      me.updateDistance_();
    };
  div.appendChild(lnk);
  
  // ���û��ر�����ʱ Google Map API ���Զ��ͷŸö��� 
  return div;
}


/**
 * �¼������������û�ѡ��ɾ����עʱ�����ã�����Ҫɾ����ñ�ע���ӵ��߶�
 * @param {GMarker} marker Ҫɾ���ı�ע
 */
GRulerControl.prototype.removePoint_ = function(marker) {
  var me = this;
  
  // �ȴӵ�ͼ��ɾ���ñ�ע
  me.map_.removeOverlay(marker);
  
  // �����м��㣬��Ҫ������ǰ���ͺ�����߶���������
  if (marker.prev_.getPoint && marker.next_.getPoint) {
    var segment = [marker.prev_.getPoint(), marker.next_.getPoint()];
    var polyline = new GPolyline(segment);
    marker.prev_.segNext_ = polyline;
    marker.next_.segPrev_ = polyline;
    me.map_.addOverlay(polyline);
  }
  marker.prev_.next_ = marker.next_;
  marker.next_.prev_ = marker.prev_;
  
  if (marker.segPrev_)
    me.map_.removeOverlay(marker.segPrev_);
  if (marker.segNext_)
    me.map_.removeOverlay(marker.segNext_);
}