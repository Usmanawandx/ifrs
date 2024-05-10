import React, { useState } from 'react';
import SmoothPinCodeInput from 'react-native-smooth-pincode-input';
import { scale } from 'react-native-size-matters';
import Colors from 'themes/colors';
import Fonts from 'themes/fonts';

const PIN = () => {
  const [code, setCode] = useState('');
  return (
    <SmoothPinCodeInput
      codeLength={6}
      value={code}
      onTextChange={setCode}
      restrictToNumbers
      cellStyle={{
        borderWidth: 2,
        borderRadius: scale(8),
        borderColor: Colors.gray10,
      }}
      cellStyleFocused={{
        borderColor: Colors.primary,
      }}
      textStyle={{
        ...Fonts.p1('medium'),
        color: Colors.gray75,
      }}
      textStyleFocused={{
        color: Colors.primar,
      }}
      containerStyle={{
        justifyContent: 'space-between',
        flexDirection: 'row',
      }}
    />
  );
};
export default PIN;
