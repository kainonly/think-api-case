import {Injectable} from '@angular/core';
import {of} from 'rxjs';

@Injectable()
export class ConfigService {
  originUrl = '';
  staticUrl = '';
  iconUrl = '';
  namespace = '';
  uploadsUrl = '';
  uploadsPath = '';
  withCredentials = false;
  httpInterceptor = false;
  interceptor = (res) => of(res);
}
