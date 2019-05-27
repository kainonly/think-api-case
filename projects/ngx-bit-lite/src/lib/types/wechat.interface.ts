export interface WechatInterface {
  config(args: {
    debug: boolean,
    appId: string,
    timestamp: number,
    nonceStr: string,
    signature: string,
    jsApiList: string[]
  }): void;

  ready(callback: () => void): void;

  error(callback: (res: any) => void): void;

  checkJsApi(args: {
    jsApiList: string[],
    success(res: any)
  }): void;
}
