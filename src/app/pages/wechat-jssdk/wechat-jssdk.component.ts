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

  /**
   * 加载
   */
  jssdk() {
    this.wechatService.ready.subscribe(wx => {
      console.log(wx);
    });
  }

  /**
   * 自定义“分享给朋友”及“分享到QQ”按钮的分享内容（1.4.0）
   */
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

  /**
   * 自定义“分享到朋友圈”及“分享到QQ空间”按钮的分享内容（1.4.0）
   */
  updateTimelineShareData() {
    this.wechatService.ready.subscribe(wx => {
      wx.updateTimelineShareData({
        title: '测试',
        link: '',
        imgUrl: '',
        success(): void {
          console.log('ok');
        }
      });
    });
  }

  /**
   * 获取“分享到腾讯微博”按钮点击状态及自定义分享内容接口
   */
  onMenuShareWeibo() {
    this.wechatService.ready.subscribe(wx => {
      wx.onMenuShareWeibo({
        title: '测试',
        desc: '',
        link: '',
        imgUrl: '',
        success(): void {
          console.log('ok');
        },
        cancel(): void {
          console.log('cancel');
        }
      });
    });
  }

  /**
   * 拍照或从手机相册中选图接口
   */
  chooseImage() {
    this.wechatService.ready.subscribe(wx => {
      wx.chooseImage({
        count: 1, // 默认9
        sizeType: ['original', 'compressed'], // 可以指定是原图还是压缩图，默认二者都有
        sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
        success(res): void {
          console.log(res);
        }
      });
    });
  }

  /**
   * 预览图片接口
   */
  previewImage() {
    this.wechatService.ready.subscribe(wx => {
      wx.previewImage({
        current: '',
        urls: []
      });
    });
  }

  /**
   * 上传图片接口
   */
  uploadImage() {
    this.wechatService.ready.subscribe(wx => {
      wx.uploadImage({
        localId: '',
        isShowProgressTips: 1,
        success(res: any): void {
          console.log('ok');
        }
      });
    });
  }

  /**
   * 下载图片接口
   */
  downloadImage() {
    this.wechatService.ready.subscribe(wx => {
      wx.downloadImage({
        serverId: '',
        isShowProgressTips: 1,
        success(): void {
          console.log('ok');
        }
      });
    });
  }

  /**
   * 获取本地图片接口
   */
  getLocalImgData() {
    this.wechatService.ready.subscribe(wx => {
      wx.getLocalImgData({
        localId: '',
        success(res): void {
          console.log(res);
        }
      });
    });
  }

  /**
   * 开始录音接口
   */
  startRecord() {
    this.wechatService.ready.subscribe(wx => {
      wx.startRecord();
    });
  }

  /**
   * 停止录音接口
   */
  stopRecord() {
    this.wechatService.ready.subscribe(wx => {
      wx.stopRecord({
        success(res: any): void {
          console.log(res);
        }
      });
    });
  }

  /**
   * 监听录音自动停止接口
   */
  onVoiceRecordEnd() {
    this.wechatService.ready.subscribe(wx => {
      wx.onVoiceRecordEnd({
        complete(res: any): void {
          console.log(res);
        }
      });
    });
  }

  /**
   * 播放语音接口
   */
  playVoice() {
    this.wechatService.ready.subscribe(wx => {
      wx.playVoice({
        localId: ''
      });
    });
  }

  /**
   * 暂停播放接口
   */
  pauseVoice() {
    this.wechatService.ready.subscribe(wx => {
      wx.pauseVoice({
        localId: ''
      });
    });
  }

  /**
   * 停止播放接口
   */
  stopVoice() {
    this.wechatService.ready.subscribe(wx => {
      wx.stopVoice({
        localId: ''
      });
    });
  }

  /**
   * 监听语音播放完毕接口
   */
  onVoicePlayEnd() {
    this.wechatService.ready.subscribe(wx => {
      wx.onVoicePlayEnd({
        success(res: any): void {
          console.log('ok');
        }
      });
    });
  }

  /**
   * 上传语音接口
   */
  uploadVoice() {
    this.wechatService.ready.subscribe(wx => {
      wx.uploadVoice({
        localId: '',
        isShowProgressTips: 1,
        success(res: any): void {
          console.log('ok');
        }
      });
    });
  }

  /**
   * 下载语音接口
   */
  downloadVoice() {
    this.wechatService.ready.subscribe(wx => {
      wx.downloadVoice({
        serverId: '',
        isShowProgressTips: 1,
        success(res: any): void {
          console.log('ok');
        }
      });
    });
  }

  /**
   * 识别音频并返回识别结果接口
   */
  translateVoice() {
    this.wechatService.ready.subscribe(wx => {
      wx.translateVoice({
        localId: '',
        isShowProgressTips: 1,
        success(res: any): void {
          console.log('ok');
        }
      });
    });
  }

  /**
   * 获取网络状态接口
   */
  getNetworkType() {
    this.wechatService.ready.subscribe(wx => {
      wx.getNetworkType({
        success(res: any): void {
          console.log(res);
        }
      });
    });
  }

  /**
   * 使用微信内置地图查看位置接口
   */
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

  /**
   * 开启查找周边ibeacon设备接口
   */
  startSearchBeacons() {
    this.wechatService.ready.subscribe(wx => {
      wx.startSearchBeacons({
        ticket: '',
        complete(argv: any): void {
          console.log(argv);
        }
      });
    });
  }

  /**
   * 关闭查找周边ibeacon设备接口
   */
  stopSearchBeacons() {
    this.wechatService.ready.subscribe(wx => {
      wx.stopSearchBeacons({
        complete(res: any): void {
          console.log(res);
        }
      });
    });
  }

  /**
   * 监听周边ibeacon设备接口
   */
  onSearchBeacons() {
    this.wechatService.ready.subscribe(wx => {
      wx.onSearchBeacons({
        complete(argv: any): void {
          console.log(argv);
        }
      });
    });
  }

  /**
   * 关闭当前网页窗口接口
   */
  closeWindow() {
    this.wechatService.ready.subscribe(wx => {
      wx.closeWindow();
    });
  }

  /**
   * 批量隐藏功能按钮接口
   */
  hideMenuItems() {
    this.wechatService.ready.subscribe(wx => {
      wx.hideMenuItems({
        menuList: []
      });
    });
  }

  /**
   * 批量显示功能按钮接口
   */
  showMenuItems() {
    this.wechatService.ready.subscribe(wx => {
      wx.showMenuItems({
        menuList: []
      });
    });
  }

  /**
   * 隐藏所有非基础按钮接口
   */
  hideAllNonBaseMenuItem() {
    this.wechatService.ready.subscribe(wx => {
      wx.hideAllNonBaseMenuItem();
    });
  }

  /**
   * 显示所有功能按钮接口
   */
  showAllNonBaseMenuItem() {
    this.wechatService.ready.subscribe(wx => {
      wx.showAllNonBaseMenuItem();
    });
  }

  /**
   * 调起微信扫一扫接口
   */
  scanQRCode() {
    this.wechatService.ready.subscribe(wx => {
      wx.scanQRCode({
        needResult: 0,
        scanType: ['qrCode', 'barCode'],
        success(res: any): void {
          console.log(res);
        }
      });
    });
  }

  /**
   * 跳转微信商品页接口
   */
  openProductSpecificView() {
    this.wechatService.ready.subscribe(wx => {
      wx.openProductSpecificView({
        productId: '',
        viewType: ''
      });
    });
  }

}

