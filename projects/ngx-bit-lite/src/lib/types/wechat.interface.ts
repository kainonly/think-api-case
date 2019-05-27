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
  chooseImage(): void;
}
