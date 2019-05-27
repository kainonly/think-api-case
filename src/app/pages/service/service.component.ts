import {Component} from '@angular/core';
import {WechatService} from 'ngx-bit-lite';
import {BitService} from 'ngx-bit-lite';

@Component({
  selector: 'app-service',
  templateUrl: './service.component.html',
  styleUrls: ['./service.component.scss']
})
export class ServiceComponent {
  constructor(private wechatService: WechatService,
              public bit: BitService) {
  }

  jssdk() {
    this.wechatService.ready.subscribe(wx => {
      console.log(wx);
    });
  }

  openLocation() {
    this.wechatService.ready.subscribe(wx => {
      wx.openLocation({
        latitude: 0,
        longitude: 0,
        name: '',
        address: '',
        scale: 1,
        infoUrl: ''
      });
    });
  }

  /**
   * 获取地理位置
   */
  getLocation() {
    this.wechatService.ready.subscribe(wx => {
      wx.getLocation({
        type: 'wgs84',
        success(res) {
          console.log(res);
        }
      });
    });
  }
}
