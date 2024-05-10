import React from 'react';
import {
  TouchableOpacity, ImageBackground, StyleSheet, View,
} from 'react-native';
import { scale } from 'react-native-size-matters';
import PropTypes from 'prop-types';
import { getScreenWidth } from 'utils/size';
import Card from '../Card';
import Text from '../Text';


const styles = StyleSheet.create({
  container: {
    borderTopLeftRadius: scale(8),
    borderTopRightRadius: scale(8),
    overflow: 'hidden',
    flex: 1,
  },
  bg: {
    flex: 1,
  },
  label: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
});
const  CheckImageOnUrl=({url})=>{
  const [istrue,setIstrue] =React.useState(false);
  React.useEffect(()=>{
    fetch(url)
  .then((res)=>{
    if (res.status==404) {
      setIstrue(false);
    }
    else{
      setIstrue(true);

    }
  })
  .catch((err)=>{
    setIstrue(false);

  })
  },[])
  
  return(
    <>
    {istrue ? <ImageBackground
      source={  {uri: url}  }
      style={styles.bg}
    />:<ImageBackground
    source={   {uri:'https://ecpmarket.mywheels.pk/assets/images/noimage.png'}  }
    style={styles.bg}
  /> }
    </>
  );
  
}

const SmallTile = ({
  images, size, offset, style, label, onPress,
  photo = null,
}) => {
  const width = (size) - scale(offset);

  const [path,setParh ]=React.useState(`https://ecpmarket.mywheels.pk/assets/images/products/${photo}`) ;

  return (
    <Card style={StyleSheet.flatten([
      {
        width,
        aspectRatio: 2 / 3,
      },
      style,
    ])}
    >
      <TouchableOpacity style={styles.container} onPress={onPress}>
        <View
          style={{
            width,
            aspectRatio: 1 / 1,
          }}
        >
                    <CheckImageOnUrl url={path}/>

        </View>
        {label && (
          <View style={styles.label}>
            <Text>{label}</Text>
          </View>
        )}
      </TouchableOpacity>
    </Card>
  );
};

SmallTile.propTypes = {
  size: PropTypes.number,
  offset: PropTypes.number,
  style: PropTypes.any,
  images: PropTypes.array,
  onPress: PropTypes.func.isRequired,
  label: PropTypes.string,
};

SmallTile.defaultProps = {
  size: getScreenWidth() / 3,
  offset: 28,
  style: null,
  label: null,
};

export default SmallTile;
