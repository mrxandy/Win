/**
 * 这个例子演示了 Google Map API 的以下功能:
 *   * 可拖拽的标注
 *   * 在地图上覆盖折线
 *   * 计算地理距离
 *   * 事件处理（单击、拖拽）
 *   * 消息提示窗口（气泡窗口）
 *   * 利用链表维护各种对象
 *   * 自定义控件
 *
 * 注意：为了在 IE6 中正常显示折线，必须在网页的 <HTML> 标签中加上：
 *   <html xmlns:v="urn:schemas-microsoft-com:vml">
 *
 * @author haogang
 */

/**
 * 本示例用一个双向链表维护用户设定的标注，能够容易的实现标注的遍历和删除
 * 每个链表结点 m 有如下字段：
 *    m.prev      前驱标注
 *    m.next      后继标注
 *    m.segPrev   连接本标注与前驱标注的线段
 *    m.segNext   连接本标注与后继标注的线段
 */
function GRulerControl() {
  var me = this;
  
  // 可国际化的字符串
  me.RESET_BUTTON_TITLE_ = '清除所有测距标注';
  me.ENABLE_BUTTON_TITLE_ = '添加测距标注已启用，单击这里禁用';
  me.DISABLE_BUTTON_TITLE_ = '添加测距标注已禁用，单击这里启用';
  me.DELETE_BUTTON_TITLE_ = '删除';
  
  me.RESET_BUTTON_IMAGE_ = 'http://www.google.com/apis/maps/demo/distcal/images/ruler_clear.png';
  me.ENABLE_BUTTON_IMAGE_ = 'http://www.google.com/apis/maps/demo/distcal/images/ruler_enabled.png';
  me.DISABLE_BUTTON_IMAGE_ = 'http://www.google.com/apis/maps/demo/distcal/images/ruler_disabled.png';
  me.BACKGROUND_IMAGE_ = 'http://www.google.com/apis/maps/demo/distcal/images/ruler_background.png'
  
  me.KILOMETER_ = '公里';
  me.METER_ = '米';
}

GRulerControl.prototype = new GControl();

/**
 * 初始化标尺控件
 */
GRulerControl.prototype.initialize = function(map) {
  var me = this;
  var container = document.createElement('div');
  me.setButtonStyle_(container);
 
  // “启用/禁用”按钮
  var btnEnable = document.createElement('img');
  btnEnable.width = btnEnable.height = 19;
  GEvent.addDomListener(btnEnable, 'click', 
    function() {
      me.setEnabled(!me.isEnabled());
    }
  );
  container.appendChild(btnEnable);
  
  // “重置”按钮
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
  
  // 距离标签
  var txtInfo = document.createElement('div');
  txtInfo.style.font = 'small Arial';
  txtInfo.style.fontWeight = 'bold';
  txtInfo.style.fontSize = '9pt';
  txtInfo.style.width = "82px";
  container.appendChild(txtInfo);
  
  // 初始化内部变量
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
 * 设置控件的格式
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
 * 用恰当的格式表示距离
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
 * 格式化角度为字符串
 */
GRulerControl.prototype.formatDegree_ = function(value) {
  value = Math.abs(value);
  var v1 = Math.floor(value);
  var v2 = Math.floor((value - v1) * 60);
  var v3 = Math.round((value - v1) * 3600 % 60);
  return v1 + '°' + v2 + '\'' + v3 + '"';
}

/**
 * 格式化经纬度为字符串
 */
GRulerControl.prototype.formatLatLng_ = function(pt) {
  var me = this;
  
  var latName, lngName;
  var lat = pt.lat();
  var lng = pt.lng();
  latName = lat >= 0 ? '北纬' : '南纬';
  lngName = lng >= 0 ? '东经' : '西经';

  return lngName + me.formatDegree_(lng) + '，' 
    + latName + me.formatDegree_(lat);
}

/**
 * 返回控件的默认位置
 */
GRulerControl.prototype.getDefaultPosition = function() {
  return new GControlPosition(G_ANCHOR_TOP_RIGHT, new GSize(8, 8));
}

/**
 * 返回控件是否已启用
 */
GRulerControl.prototype.isEnabled = function() {
  return this.enabled_;
}

/**
 * 设置控件的“启用/禁用"状态
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
 * 事件处理函数：当用户单击地图时，要在该位置添加一个标注
 */
GRulerControl.prototype.onMapClick_ = function(marker, point) {
  var me = this.rulerControl_;
  
  // 如果用户单击的是标注，不再这里处理
  if (marker)
    return;

  // 创建标注，并添加到链表中
  var newMarker = new GMarker(point, {draggable: true});

  var pos = me.tail_.prev_;
  newMarker.prev_ = pos;
  newMarker.next_ = pos.next_;
  pos.next_.prev_ = newMarker;
  pos.next_ = newMarker;

  // 为标注添加事件处理函数：拖拽标注时要更新连接线段和距离
  GEvent.addListener(newMarker, 'dragend',
    function() {
      me.map_.closeInfoWindow();
      me.updateSegments_(newMarker);
      me.updateDistance_();
    }
  );
  // 为标注添加事件处理函数：单击标注时要显示气泡窗口
  GEvent.addListener(newMarker, 'click',
    function() {
      newMarker.openInfoWindow(me.createInfoWindow_(newMarker));
    }
  );
  
  // 将创建的标注添加到地图中
  me.map_.addOverlay(newMarker);

  if (newMarker.prev_ != me.head_) {
    // 如果这不是第一个标注，则创建连接到上一个标注的线段，并显示在地图中
    var segment = [newMarker.prev_.getPoint(), point];
    newMarker.segPrev_ = new GPolyline(segment);
    newMarker.prev_.segNext_ = newMarker.segPrev_;
    me.map_.addOverlay(newMarker.segPrev_);

    // 更新距离显示
    me.updateDistance_();
  }
}

/**
 * 统计总距离，并显示在网页中
 */
GRulerControl.prototype.updateDistance_ = function() {
  var me = this;
  var len = me.getDistance();
  
  // 结果显示在网页中
  me.txtInfo_.innerHTML = me.formatDistance_(len);
}

/**
 * 遍历链表，统计总距离
 */
GRulerControl.prototype.getDistance = function() {
  var me = this;
  var len = 0;
  
  // 周游链表，累计相邻两个标注间的距离
  for (var m = me.head_; m != me.tail_; m = m.next_) {
    if (m.prev_ && m.prev_.getPoint)
      len += m.prev_.getPoint().distanceFrom(m.getPoint());
  }
  return len;
}

/**
 * 清除所有标注，初始化链表
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
 * 事件处理函数：当用户拖拽标注、标注坐标改变时被调用，这里要更新与该标注连接的线段
 * @param {GMarker} marker 被拖拽的标注
 */
GRulerControl.prototype.updateSegments_ = function(marker) {
  var me = this;
  var segment;
  
  // 更新连接前驱的线段
  if (marker.segPrev_ && marker.prev_.getPoint) {
    // 从地图上删除旧的线段
    me.map_.removeOverlay(marker.segPrev_);
    
    // 根据标注的当前坐标构造新的线段，并更新链表结点的相关字段
    segment = [marker.prev_.getPoint(), marker.getPoint()];
    marker.segPrev_ = new GPolyline(segment);
    marker.prev_.segNext_ = marker.segPrev_;
    
    // 将新线段添加到地图中
    me.map_.addOverlay(marker.segPrev_);
  }
  
  // 更新连接后继的线段，与上类似
  if (marker.segNext_ && marker.next_.getPoint) {
    me.map_.removeOverlay(marker.segNext_);
    segment = [marker.getPoint(), marker.next_.getPoint()];
    marker.segNext_ = new GPolyline(segment);
    marker.next_.segPrev_ = marker.segNext_;
    me.map_.addOverlay(marker.segNext_);
  }
}


/**
 * 为气泡提示窗口创建 DOM 对象，包括标注的坐标和“删除”按钮
 * @param {GMarker} marker 对应的标注
 */
GRulerControl.prototype.createInfoWindow_ = function(marker) {
  var me = this;
  
  // 为气泡提示窗口创建动态 DOM 对象，这里我们用 DIV 标签
  var div = document.createElement('div');
  div.style.fontSize = '10.5pt';
  div.style.width = '250px';
  div.appendChild(
    document.createTextNode(me.formatLatLng_(marker.getPoint())));
    
  var hr = document.createElement('hr');
  hr.style.border = 'solid 1px #cccccc';
  div.appendChild(hr);
  
  // 创建“删除”按钮
  var lnk = document.createElement('div');
  lnk.innerHTML = me.DELETE_BUTTON_TITLE_;
  lnk.style.color = '#0000cc';
  lnk.style.cursor = 'pointer';
  lnk.style.textDecoration = 'underline';
  
  // 为“删除”按钮添加事件处理：调用 removePoint() 并重新计算距离
  lnk.onclick =
    function() {
      me.map_.closeInfoWindow();
      me.removePoint_(marker);
      me.updateDistance_();
    };
  div.appendChild(lnk);
  
  // 当用户关闭气泡时 Google Map API 会自动释放该对象 
  return div;
}


/**
 * 事件处理函数：当用户选择删除标注时被调用，这里要删除与该标注连接的线段
 * @param {GMarker} marker 要删除的标注
 */
GRulerControl.prototype.removePoint_ = function(marker) {
  var me = this;
  
  // 先从地图上删除该标注
  me.map_.removeOverlay(marker);
  
  // 对于中间结点，还要把它的前驱和后继用线段连接起来
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