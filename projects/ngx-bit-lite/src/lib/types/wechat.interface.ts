export interface WechatInterface {
  /**
   * 通过config接口注入权限验证配置
   */
  config(args: {
    debug: boolean,
    appId: string,
    timestamp: number,
    nonceStr: string,
    signature: string,
    jsApiList: string[]
  }): void;

  /**
   * 通过ready接口处理成功验证
   */
  ready(callback: () => void): void;

  /**
   * 通过error接口处理失败验证
   */
  error(callback: (res: any) => void): void;

  /**
   * 判断当前客户端版本是否支持指定JS接口
   */
  checkJsApi(args: {
    jsApiList: string[],
    success(res: any)
  }): void;

  /**
   * 自定义“分享给朋友”及“分享到QQ”按钮的分享内容
   */
  updateAppMessageShareData(args: {
    title: string,
    desc: string,
    link: string,
    imgUrl: string,
    success(): void
  }): void;

  /**
   * 自定义“分享到朋友圈”及“分享到QQ空间”按钮的分享内容
   */
  updateTimelineShareData(args: {
    title: string,
    link: string,
    imgUrl: string,
    success(): void
  }): void;

  /**
   * 获取“分享到腾讯微博”按钮点击状态及自定义分享内容接口
   */
  onMenuShareWeibo(args: {
    title: string,
    desc: string,
    link: string,
    imgUrl: string,
    success(): void,
    cancel(): void
  }): void;

  /**
   * 拍照或从手机相册中选图接口
   */
  chooseImage(args: {
    count: number,
    sizeType: string[],
    sourceType: string[],
    success(res: any): void
  }): void;

  /**
   * 预览图片接口
   */
  previewImage(args: {
    current: string,
    urls: string[]
  }): void;

  /**
   * 上传图片接口
   */
  uploadImage(args: {
    localId: string,
    isShowProgressTips: number,
    success(res: any): void
  }): void;

  /**
   * 下载图片接口
   */
  downloadImage(args: {
    serverId: string,
    isShowProgressTips: number,
    success(res: any): void
  }): void;

  /**
   * 获取本地图片接口
   */
  getLocalImgData(args: {
    localId: string,
    success(res: any): void
  }): void;

  /**
   * 开始录音接口
   */
  startRecord(): void;

  /**
   * 停止录音接口
   */
  stopRecord(args: {
    success(res: any): void
  }): void;

  /**
   * 监听录音自动停止接口
   */
  onVoiceRecordEnd(args: {
    complete(res: any): void
  }): void;

  /**
   * 播放语音接口
   */
  playVoice(args: {
    localId: string
  }): void;

  /**
   * 暂停播放接口
   */
  pauseVoice(args: {
    localId: string
  }): void;

  /**
   * 停止播放接口
   */
  stopVoice(args: {
    localId: string
  }): void;

  /**
   * 监听语音播放完毕接口
   */
  onVoicePlayEnd(args: {
    success(res: any): void
  }): void;

  /**
   * 上传语音接口
   */
  uploadVoice(args: {
    localId: string,
    isShowProgressTips: number,
    success(res: any): void
  }): void;

  /**
   * 下载语音接口
   */
  downloadVoice(args: {
    serverId: string,
    isShowProgressTips: number,
    success(res: any): void
  }): void;
}
