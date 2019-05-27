import {Component, OnInit} from '@angular/core';
import {WechatService} from 'ngx-bit-lite';
import {BitService} from 'ngx-bit-lite';

@Component({
  selector: 'app-wechat-sdk',
  templateUrl: './wechat-sdk.component.html',
  styleUrls: ['./wechat-sdk.component.scss']
})
export class WechatSdkComponent implements OnInit {
  constructor(private wechatService: WechatService,
              public bit: BitService) {
  }

  ngOnInit(): void {
    this.wechatService.InstallPlugin('wechat/jssdk');
    this.wechatService.error.subscribe(res => {
      console.log(res);
    });
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
