import {Component} from '@angular/core';
import {Router} from '@angular/router';
import {Toast} from 'ng-zorro-antd-mobile';

@Component({
  selector: 'app-home',
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.scss']
})
export class HomeComponent {
  notice: any = {
    content: '我是小黄条，小黄条的小，小黄条的黄，小黄条的条，请多多关照！！！',
    font: '14px'
  };

  state: any = {
    data: ['AiyWuByWklrrUDlFignR', 'TekJlZRVCjLFexlOCuWn', 'IJOtIlfsYdTyaDTRVrLI'],
    imgHeight: '184px'
  };

  constructor(private router: Router,
              private _: Toast) {
  }

  wechatJSSDK() {
    this.router.navigateByUrl('/wechat-jssdk');
  }

  openScenes() {
    this.router.navigateByUrl('/scenes');
  }

  feedback() {
    Toast.success('ok');
  }
}
