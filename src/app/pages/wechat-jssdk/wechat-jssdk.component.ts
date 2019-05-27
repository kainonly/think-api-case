import {Component, OnInit} from '@angular/core';
import {WechatService} from 'ngx-bit-lite';
import {BitService} from 'ngx-bit-lite';

@Component({
  selector: 'app-wechat-jssdk',
  templateUrl: './wechat-jssdk.component.html',
  styleUrls: ['./wechat-jssdk.component.scss']
})
export class WechatJssdkComponent implements OnInit {
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

  updateAppMessageShareData() {
    this.wechatService.ready.subscribe(wx => {
      wx.updateAppMessageShareData({
        title: '测试',
        desc: 'DESC',
        link: 'https://',
        imgUrl: '',
        success(): void {
          console.log('ok');
        }
      });
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
