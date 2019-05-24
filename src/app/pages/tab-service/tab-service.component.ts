import {Component} from '@angular/core';
import {WechatService} from '../../api/wechat.service';

@Component({
  selector: 'app-tab-service',
  templateUrl: './tab-service.component.html',
  styleUrls: ['./tab-service.component.scss']
})
export class TabServiceComponent {
  constructor(private wechatService: WechatService) {

  }

  loadSDK() {
    this.wechatService.jssdk().subscribe(data => {
      console.log(data);
    });
  }
}
